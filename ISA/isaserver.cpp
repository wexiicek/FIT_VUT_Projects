/********************************************
 *  ISA 2019/2020 Project                   *
 *  HTTP Board                              *
 *  - server                                *
 *                                          *
 * Author: Dominik Juriga (xjurig00)        *
 *******************************************/

#include <stdio.h>
#include <sys/socket.h>
#include <unistd.h>
#include <stdlib.h>
#include <netinet/in.h>
#include <string.h>
#include <iostream>
#include <vector>
#include <sstream>
#include <iterator>

#include "http_message.hpp"
#include "board.hpp"

#define MAX_CONNECTIONS 5
#define MAX_SIZE 16385
#define PORT_MAX 65535
#define PORT_MIN 1024

// Helper functions

/**
 * End the program with error message
 * 
 * @param message Message to display
 */
void error(const char *message)
{
    perror(message);
    exit(EXIT_FAILURE);
}

/**
 * Check if the char array can be converted into integer
 * 
 * @param var Array to be checked
 * @return true If the array contains numbers
 */
bool check_for_integer(const char *var)
{
    char *end;
    int input = strtol(var, &end, 10);
    (void)input; // "unused variable" hot-fix
    if (*end != '\0')
    {
        return false;
    }
    return true;
}

/**
 * Split input string by a space into a vector of tokens
 * 
 * @param source Source string 
 * @return Vector of tokens from the source string
 */
std::vector<std::string> tokenize(std::string source)
{
    std::istringstream src(source);
    std::vector<std::string> tokens((std::istream_iterator<std::string>(src)), std::istream_iterator<std::string>());

    return tokens;
}

/**
 * Split input string by a delimiter into a vector of tokens
 * 
 * @param source Source string 
 * @param delimiter Delmiter to split by
 * @return Vector of tokens from the source string
 */
std::vector<std::string> tokenize_by_delimiter(std::string source, char delimiter)
{
    std::vector<std::string> tokens;
    std::string temp;
    std::istringstream src(source);

    while (std::getline(src, temp, delimiter)) // Load a token into temp variable
    {
        if (temp.find_first_not_of(' ') != std::string::npos) // While there are nonwhitespace elements before the end of the string
        {
            tokens.push_back(temp); // Push the token into the vector
        }
    }
    return tokens;
}

/**
 * Checks if input string contains only a-zA-Z0-9
 * 
 * @param src Input string
 * @return bool True if there are no special characters
 */
bool is_al_num(std::string src)
{
    unsigned int i = 0;
    while (isalnum(src.c_str()[i]))
        i++;
    return i == src.length();
}

/**
 * Finds the content length in the vector of tokens
 * 
 * @param tokens Vector of tokens to search
 * @return Content length of request body
 */
int find_content_length(std::vector<std::string> tokens)
{
    bool found = false;
    for (unsigned int i = 0; i < tokens.size(); i++)
    {

        if (found)
        {
            return std::atoi(tokens.at(i).c_str());
        }
        if (!strcmp(tokens.at(i).c_str(), "Content-Length:"))
        {
            found = true;
        }
    }
    return -1;
}

/**
 * Find the position of the board in list
 * 
 * @param name The board name to search for
 * @param boards List of boards
 * @return Position in list
 */
int find_board(std::string name, std::list<board> boards)
{
    int counter = 0;
    if (boards.size() > 0)
    {
        for (auto brds = boards.begin(); brds != boards.end(); brds++)
        {
            if ((*brds).name == name)
            {
                return counter;
            }
            counter++;
        }
    }
    return -1;
}

// Program functions

/**
 * Decide whether the program should continue or end
 * 
 * @param argc Argument count
 * @parm argv Array of arguments
 * @return true if the arguments are valid 
 */
bool continue_program(int argc, char const *argv[])
{
    if (argc == 2 && !strcmp("-h", argv[1]))
    {
        std::cout << "\
Usage: ./isaserver [-p port_num] (-h)\n\
-p [mandatory] specifies the port number to run on\n\
-h (optional) shows this help message\n\
\n\
This program serves as an API for boards and their posts.\n\
More info can be found in manual.pdf or README file\n\
packed with this program.\n";
        return false;
    }
    else if (argc == 3)
    {
        if (!strcmp("-p", argv[1]) && check_for_integer(argv[2]))
        {
            return true;
        }
    }
    error("Server cannot run with invalid arguments. Please see -h for help.");
    return false;
}

/**
 * Parse arguments. Since we already know the arguments are valid, 
 * we only return the port number
 * 
 * @param argv Array of arguments
 * @return Port number
 */
int parse_arguments(char const *argv[])
{
    int port = std::atoi(argv[2]);
    if (port < PORT_MIN || port > PORT_MAX)
    {
        error("Port is not in valid range. Please see -h for help.");
    }
    return port;
}

/**
 * Find the payload in a HTTP message
 * 
 * @param message Source string (HTTP message)
 * @return Substring containing the payload
 */
std::string find_payload(std::string message)
{
    // Find the empty line in the message and return the remainder after it
    return message.substr(message.find("\r\n\r\n") + 4, message.length() - 1).append("\n");
}

/**
 * Handle GET request (List all boards or posts)
 * 
 * @param address The address of the HTTP request
 * @param boards List of boards
 * @param msg Response message struct
 * @return HTTP Response code
 */
int verify_GET(std::vector<std::string> address, std::list<board> boards, http_msg &msg)
{
    if (address.size() == 1)
    {
        // GET /boards == List all the boards
        if (address.at(0) == "boards")
        {
            if (boards.size() > 0)
            {
                for (auto brds = boards.begin(); brds != boards.end(); brds++)
                {
                    // Iterate through all the boards and append their name to the payload
                    msg.PAYLOAD.append((*brds).name).append("\n");
                }
                return HTTP_OK;
            }
        }
    }
    else if (address.size() == 2)
    {
        if (address.at(0) == "board")
        {
            // GET /board/name == list all the posts of a board
            int position = find_board(address.at(1), boards); // Find the position of the boardname
            if (position >= 0)
            {
                // If the board exists, grab the structure into a variable
                auto current_board = *std::next(boards.begin(), position);

                if (current_board.posts.size() > 0)
                {
                    msg.PAYLOAD = "[" + address.at(1) + "]\n";
                    for (auto psts = current_board.posts.begin(); psts != current_board.posts.end(); psts++)
                    {
                        // Iterate through the posts of a board and append them into the msg payload
                        std::string temp = std::to_string((*psts).id).append(". ").append((*psts).text);

                        // Remove unnecessary endlines from the messages
                        std::string::size_type pos = 0;
                        while ((pos = temp.find("\r\n", pos)) != std::string::npos)
                        {
                            temp.erase(pos, 2);
                        }
                        msg.PAYLOAD.append(temp);
                    }
                    return HTTP_OK;
                }
            }
        }
    }
    return HTTP_NOT_FOUND;
}

/**
 * Handle POST request (Add a board or post)
 * 
 * @param address The address of the HTTP request
 * @param boards List of boards
 * @param req_payload Payload of the request
 * @return HTTP response code
 */
int verify_POST(std::vector<std::string> address, std::list<board> &boards, std::string req_payload, int clen)
{
    if (address.size() == 2)
    {
        // POST /boards == add a new board
        if (address.at(0) == "boards")
        {
            std::string board_name = address.at(1);
            if (find_board(board_name, boards) >= 0)
            {
                // If the board already exists, there is a conflict
                return HTTP_CONFLICT;
            }
            if (is_al_num(board_name))
            {
                board new_board;
                new_board.name = board_name; // Create a new board with the given name
                boards.push_back(new_board); // Push the new board into the list
                return HTTP_CREATED;
            }
        }

        // POST /board/name
        else if (address.at(0) == "board")
        {
            if (clen <= 0)
            {
                return HTTP_BAD_REQUEST;
            }
            int position = find_board(address.at(1), boards);
            if (position >= 0)
            {
                // Grab the current board into a variable
                board current_board = (position > 0) ? (*std::next(boards.begin(), position)) : boards.front();

                // Create the new post
                post new_post;
                new_post.text = req_payload;
                new_post.id = current_board.last_id;

                // Insert the new post into the board
                if (position > 0)
                {
                    (*std::next(boards.begin(), position)).last_id = current_board.last_id + 1;
                    (*std::next(boards.begin(), position)).posts.push_back(new_post);
                }
                else
                {
                    boards.front().last_id = current_board.last_id + 1;
                    boards.front().posts.push_back(new_post);
                }

                return HTTP_CREATED;
            }
        }
    }
    return HTTP_NOT_FOUND;
}

/**
 * Handle DELETE request (Delete a board or post)
 * 
 * @param address The address of the HTTP request
 * @param boards List of boards
 * @return HTTP response code
 */
int verify_DELETE(std::vector<std::string> address, std::list<board> &boards)
{
    if (address.size() == 2)
    {
        // DElETE /boards/name == delete board with given name
        int position = find_board(address.at(1), boards);
        if (position >= 0)
        {
            boards.erase(std::next(boards.begin(), position));
            return HTTP_OK;
        }
    }
    else if (address.size() == 3)
    {
        // DELETE /board/name/id == delete a post with on board name with given id
        std::string board_name = address.at(1);

        // Check if the id is a valid integer
        int post_id = check_for_integer(address.at(2).c_str()) ? std::atoi(address.at(2).c_str()) : -1;
        if (post_id > 0)
        {
            for (auto board = boards.begin(); board != boards.end(); board++)
            {
                // Iterate through boards and find the one with the name
                if (board_name == (*board).name)
                {
                    // Iterate through posts on found board
                    for (auto post = (*board).posts.begin(); post != (*board).posts.end(); post++)
                    {
                        if ((*post).id == post_id)
                        {
                            // If the post has been found, remove it
                            (*board).posts.erase(post);

                            // If the post has been removed, renumber all the posts from 1
                            int new_id = 1;
                            for (auto post = (*board).posts.begin(); post != (*board).posts.end(); post++)
                            {
                                (*post).id = new_id++;
                            }
                            (*board).last_id = (*board).posts.size() > 0 ? new_id : 1;
                            return HTTP_OK;
                        }
                    }
                }
            }
        }
    }
    return HTTP_NOT_FOUND;
}

/**
 * Handle PUT request (Update post text)
 * 
 * @param address The address of the HTTP request
 * @param boards List of boards
 * @param req_payload Payload of the request
 * @return HTTP response code
 */
int verify_PUT(std::vector<std::string> address, std::list<board> &boards, std::string req_payload, int clen)
{
    if (address.size() == 3)
    {
        // PUT /board/name/id == update a post with on board name with given id
        std::string board_name = address.at(1);

        if (clen <= 0)
        {
            return HTTP_BAD_REQUEST;
        }

        // Check if the id is a valid integer
        int post_id = check_for_integer(address.at(2).c_str()) ? std::atoi(address.at(2).c_str()) : -1;
        if (post_id > 0)
        {
            for (auto board = boards.begin(); board != boards.end(); board++)
            {
                // Iterate through boards and find the one with the name
                if (board_name == (*board).name)
                {
                    // Iterate through posts on found board
                    for (auto post = (*board).posts.begin(); post != (*board).posts.end(); post++)
                    {
                        if ((*post).id == post_id)
                        {
                            // If the post has been found, update it
                            (*post).text = req_payload;
                            return HTTP_OK;
                        }
                    }
                }
            }
        }
    }
    return HTTP_NOT_FOUND;
}

/**
 * This is a HTTP message template
 * 
 * @param msg HTTP message containing the mandatory data
 * @return String containing HTTP response
 */
std::string generate_response_string(http_msg msg)
{
    std::string response = "";

    switch (msg.RESPONSE)
    {
    case HTTP_OK:
        msg.RESPONSE_TEXT = HTTP_OK_T;
        break;

    case HTTP_CREATED:
        msg.RESPONSE_TEXT = HTTP_CREATED_T;
        break;

    case HTTP_NOT_FOUND:
        msg.RESPONSE_TEXT = HTTP_NOT_FOUND_T;
        break;

    case HTTP_BAD_REQUEST:
        msg.RESPONSE_TEXT = HTTP_BAD_REQUEST_T;
        break;

    case HTTP_CONFLICT:
        msg.RESPONSE_TEXT = HTTP_CONFLICT_T;
        break;
    }

    // First line of http msg, e.g.: HTTP/1.1 200 OK
    response.append(HTTP_VERSION).append(" ").append(std::to_string(msg.RESPONSE)).append(" ").append(msg.RESPONSE_TEXT).append(HTTP_LINE_END);
    if (msg.CONTENT_LENGTH > 0)
    {
        // Content type line
        response.append(HTTP_CONTENT_TYPE).append(" ").append(HTTP_TEXT_PLAIN).append(HTTP_LINE_END);
        // Content length line
        response.append(HTTP_CONTENT_LENGTH).append(" ").append(std::to_string(msg.CONTENT_LENGTH)).append(HTTP_LINE_END);
        response.append(HTTP_LINE_END); // Empty line dividing header and payload
        std::string::size_type index = 0;

        // Newlines entered throught the terminal are being escaped automatically
        // Look throught the text for "\n" and replace them with newlinws
        while ((index = msg.PAYLOAD.find("\\n", index)) != std::string::npos)
        {
            msg.PAYLOAD.replace(index, 2, "\n");
            index += 1;
        }

        response.append(msg.PAYLOAD).append(HTTP_LINE_END);
    }
    else
    {
        response.append(HTTP_LINE_END);
    }
    return response;
}

/**
 *  Check the request for validity and generate 
 *  adequate response
 * 
 * @param request Client request string
 * @param boards Available boards
 * @return String containing HTTP response
 */
std::string create_response(std::string request, std::list<board> &boards)
{
    std::vector<std::string> tokens = tokenize(request); // Split request into tokens
    std::string method = "FAULT";                        // Default state
    if (tokens.size() > 0)
    {
        method = tokens.at(0); // If the request string is empty
    }

    std::vector<std::string> address = tokenize_by_delimiter(tokens.at(1), '/'); // Split the address into tokens
    if (address.size() <= 0)
    {
        method = "FAULT"; // Will result in returning 404 response
    }
    std::string req_payload = find_payload(request); // Find the payload in the request
    http_msg msg;
    msg.PAYLOAD = "";

    // Perform adrequate actions based on the request method
    if (method == HTTP_GET)
    {
        msg.RESPONSE = verify_GET(address, boards, msg);
    }
    else if (method == HTTP_POST)
    {
        msg.RESPONSE = verify_POST(address, boards, req_payload, find_content_length(tokens));
    }
    else if (method == HTTP_DELETE)
    {
        msg.RESPONSE = verify_DELETE(address, boards);
    }
    else if (method == HTTP_PUT)
    {
        msg.RESPONSE = verify_PUT(address, boards, req_payload, find_content_length(tokens));
    }
    else
    {
        // If the method doesnt match any of the supported ones, return 400
        msg.RESPONSE = HTTP_BAD_REQUEST;
    }
    msg.CONTENT_LENGTH = msg.PAYLOAD.length() - 1;
    return generate_response_string(msg);
}

/**
 * Infinitely listen on given port and handle requests
 * 
 * @param server_fd Socket descriptor
 * @param address Address of the socket
 * @param addrlen Length of the address
 */
void await_connections(int &server_fd, sockaddr_in &address, int addrlen)
{
    int new_socket;
    char buffer[MAX_SIZE];
    std::list<board> boards;
    while (true)
    {
        if ((new_socket = accept(server_fd, (struct sockaddr *)&address, (socklen_t *)&addrlen)) < 0)
        {
            error("Cannot accept connection.");
        }

        bzero(buffer, MAX_SIZE);                // Clear the buffer
        read(new_socket, buffer, MAX_SIZE - 1); // Read data from the socket
        std::string req_str(buffer);
        bzero(buffer, MAX_SIZE); // Clear the buffer
        if (!req_str.empty())
        {
            // If there is a request, handle it and create a response
            std::string response = create_response(req_str, boards);
            // Send the response to the socket
            write(new_socket, response.c_str(), response.length());
        }
        // Close the socket
        close(new_socket);
    }
}

/**
 * Create a socket and initiate an infinite loop 
 * that receives HTTP requests
 * 
 * @param port Port to listen on
 */
void server_listen(int port)
{
    int server_fd = socket(AF_INET, SOCK_STREAM, 0);
    if (server_fd <= 0)
    {
        // Socket descriptor could not be created
        error("Cannot create socket.");
    }

    // Declaring network variables for the socket
    struct sockaddr_in address;
    int addrlen = sizeof(address);
    address.sin_family = AF_INET;         // IPv4
    address.sin_addr.s_addr = INADDR_ANY; // Listen to all local interfaces
    address.sin_port = htons(port);       // Convert port to network byte order

    bzero(address.sin_zero, sizeof(address.sin_zero)); // Erase all the data

    if (bind(server_fd, (struct sockaddr *)&address, sizeof(address)) < 0)
    {
        error("Cannot bind address to socket.");
    }

    if (listen(server_fd, MAX_CONNECTIONS) < 0)
    {
        error("Cannot listen to socket.");
    }

    //Initiate the infinite loop
    await_connections(server_fd, address, addrlen);
}

int main(int argc, char const *argv[])
{
    int port = (continue_program(argc, argv) ? parse_arguments(argv) : -1);
    //If there is no error or -h, parse arguments (in this case the only argument is port num)

    if (port >= 0)
    {
        server_listen(port);
        //If the parsing function returned positive port number, try to open a socket with this port num
    }
    return EXIT_SUCCESS;
}
