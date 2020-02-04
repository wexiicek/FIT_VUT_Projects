/********************************************
 *  ISA 2019/2020 Project                   *
 *  HTTP Board                              *
 *  - client                                *
 *                                          *
 * Author: Dominik Juriga (xjurig00)        *
 *******************************************/

#include <stdio.h>
#include <sys/socket.h>
#include <stdlib.h>
#include <unistd.h>
#include <netinet/in.h>
#include <string.h>
#include <arpa/inet.h>
#include <netdb.h>
#include <iostream>
#include <vector>
#include <sstream>
#include <iterator>
#include "target.hpp"

#define MAX_SIZE 1024
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
    (void)input;
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
 * Find the token containing the content length of HTTP message
 * 
 * @param src Source string
 * @return Content length of HTTP message
 */
int find_content_length(std::string src)
{
    std::vector<std::string> tokens = tokenize(src);
    for (unsigned int i = 0; i < tokens.size(); i++)
    {
        if (tokens.at(i) == "Content-Length:")
        {
            if (check_for_integer(tokens.at(i + 1).c_str()))
            {
                return stoi(tokens.at(i + 1));
            }
        }
    }
    return 0;
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

    while (std::getline(src, temp, delimiter))
    {
        if (temp.find_first_not_of(' ') != std::string::npos)
        {
            tokens.push_back(temp);
        }
    }
    return tokens;
}

// Program functions

/**
 * Generates HTTP message by inserting values into HTTP template
 * 
 * @param req Request that should be converted into string
 * @return String containing the request
 */
std::string generate_request_string(http_msg req)
{
    std::string request = "";
    request.append(req.METHOD).append(" ").append(req.ADDRESS).append(" ").append(req.VERSION).append(HTTP_LINE_END);
    request.append(HTTP_CONTENT_LENGTH).append(" ").append(std::to_string(req.CONTENT_LENGTH)).append(HTTP_LINE_END);
    request.append(HTTP_CONTENT_TYPE).append(" ").append(req.CONTENT_TYPE).append(HTTP_LINE_END);
    if (req.CONTENT_LENGTH > 0)
    {
        request.append(HTTP_LINE_END).append(req.PAYLOAD).append(HTTP_LINE_END);
    }
    return request;
}

/**
 * Establish a connection with the server, send a message and receive a response
 * 
 * Parts of this function related to networking have been
 * inspired by:
 *  Author: Sascha Nitsch
 *  Available online at:
 *      http://www.linuxhowtos.org/C_C++/socket.htm
 * 
 * @param target Target device structure, contains hostname and port
 */
int send_request_receive_response(target target)
{
    int sock = 0;
    char buffer[MAX_SIZE] = {0};
    if ((sock = socket(AF_INET, SOCK_STREAM, 0)) < 0)
    {
        error("Cannot create a SOCKET.");
    }

    struct sockaddr_in serv_addr;
    memset(&serv_addr, '0', sizeof(serv_addr));
    serv_addr.sin_family = AF_INET;
    serv_addr.sin_port = htons(target.port);
    if (target.request.HOST.empty())
    {
        error("No host.");
    }
    struct hostent *host = gethostbyname(target.request.HOST.c_str());
    if (host == nullptr)
    {
        error("Cannot translate this address.");
    }
    bcopy((char *)host->h_addr, (char *)&serv_addr.sin_addr.s_addr, host->h_length);

    if (connect(sock, (struct sockaddr *)&serv_addr, sizeof(serv_addr)) < 0)
    {
        error("Cannot connect to the HOST.");
    }

    std::string request_string = generate_request_string(target.request);
    send(sock, request_string.c_str(), request_string.length(), 0);

    std::string req_str = "";
    int msgs_received = 0;
    unsigned int content_length = 0, header_length = 0;
    std::string temp;
    while (true)
    {
        bzero(buffer, MAX_SIZE);
        int msg_size = recv(sock, buffer, MAX_SIZE - 1, MSG_DONTWAIT);
        if (msg_size > 0)
        {
            msgs_received++;
        }
        temp = buffer;

        if (msgs_received == 1)
        {
            header_length = temp.substr(0, temp.find("\r\n\r\n") + 4).length();
            content_length = find_content_length(temp);
        }

        if (msg_size < 0 && req_str.length() == 0)
        {
            continue;
        }
        else
        {
            req_str += temp;
        }

        if (req_str.length() > (content_length + header_length))
        {
            break;
        }
    }

    std::string res_header = req_str.substr(0, req_str.find("\r\n\r\n"));
    std::string res_payload = req_str.substr(req_str.find("\r\n\r\n") + 4, req_str.length() - 1);

    // Remove unnecessary endlines from the messages
    std::string::size_type pos = 0;
    while ((pos = res_payload.find("\r\n", pos)) != std::string::npos)
    {
        res_payload.erase(pos, 2);
    }

    std::cerr << res_header << std::endl;
    std::cout << res_payload << std::endl;
    return res_header[9] == '2' ? 0 : -1;
}

/**
 * Check the arguments, if the arguments are correct and if the
 * program should continue
 * 
 * @param argc Argument count
 * @param argv Array of arguments
 * @return true If the program should continue and the args are valid
 */
bool continue_program(int argc, char **argv)
{
    bool H_FLAG = false, P_FLAG = false; // Header and Port flags
    for (int i = 0; i < argc; i++)
    {
        if (!strcmp(argv[i], "-H"))
        {
            H_FLAG = true; // If the header switch is present
        }
        else if (!strcmp(argv[i], "-p"))
        {
            if (argv[i + 1] == nullptr || !check_for_integer(argv[i + 1]) || std::atoi(argv[i + 1]) > PORT_MAX || std::atoi(argv[i + 1]) < PORT_MIN)
            {
                error("Please enter valid port number.");
            }

            P_FLAG = true; // If the port switch is present and the value is a valid integer
        }
        else if (!strcmp(argv[i], "-h"))
        {
            std::cout << "\
Usage: ./isaclient [-H host_name] [-p port_num] [command]\n\
-H [mandatory] specifies the adress/host name of target device\n\
-p [mandatory] specifies the port of target device <1024-65535>\n\
command [mandatory] command to be performed\n\
\n\
This program serves as an interface over HTTP Board Server.\n\
It allows users to perform CRUD actions over boards and their posts.\n\
More info can be found in manual.pdf or README file\n\
packed with this program.\n";
            return false;
        }
    }

    if (!(H_FLAG && P_FLAG))
    {
        error("Invalid arguments. Please see -h for help.");
    }

    return true;
}

/**
 * Creates a HTTP message with PUT method
 * 
 * @param argc Argument count
 * @param argv Program arguments
 * @return HTTP message with PUT method
 */
http_msg create_PUT_request(int argc, char **argv)
{
    if (argc != 10)
    {
        error("Invalid arguments. Please see -h for help.");
    }
    http_msg request;
    request.METHOD = HTTP_PUT;
    request.ADDRESS.append("/board/").append(argv[optind + 2]).append("/").append(argv[optind + 3]);
    request.PAYLOAD = argv[optind + 4];
    return request;
}

/**
 * Creates a HTTP message with DELETE method
 * 
 * @param to_delete Switch between deleting a board or post
 * @param argc Argument count
 * @param argv Program arguments
 * @return HTTP message with DELETE method
 */
http_msg create_DELETE_request(int to_delete, int argc, char **argv)
{
    http_msg request;
    if (to_delete == DELETE_BOARD)
    {
        if (argc != 8)
        {
            error("Invalid arguments. Please see -h for help.");
        }
        request.ADDRESS.append("/boards/").append(argv[optind + 2]);
    }
    else if (to_delete == DELETE_POST)
    {
        if (argc != 9)
        {
            error("Invalid arguments. Please see -h for help.");
        }
        request.ADDRESS.append("/board/").append(argv[optind + 2]).append("/").append(argv[optind + 3]);
    }
    request.METHOD = HTTP_DELETE;
    return request;
}

/**
 * Creates a HTTP message with POST method
 * 
 * @param to_add Switch between adding a board or post
 * @param argc Argument count
 * @param argv Program arguments
 * @return HTTP message with POST method
 */
http_msg create_POST_request(int to_add, int argc, char **argv)
{
    http_msg request;
    if (to_add == ADD_BOARD)
    {
        if (argc != 8)
        {
            error("Invalid arguments. Please see -h for help.");
        }
        request.ADDRESS.append("/boards/").append(argv[optind + 2]);
    }
    else if (to_add == ADD_POST)
    {
        if (argc != 9)
        {
            error("Invalid arguments. Please see -h for help.");
        }
        request.ADDRESS.append("/board/").append(argv[optind + 2]);
        request.PAYLOAD = argv[optind + 3];
    }
    request.METHOD = HTTP_POST;
    return request;
}

/**
 * Creates a HTTP message with GET method
 * 
 * @param to_list Switch between listing boards and posts
 * @param name Board name to list posts from
 * @return HTTP message with GET method
 */
http_msg create_GET_request(int to_list, std::string name)
{
    http_msg request;
    if (to_list == LIST_BOARDS)
    {

        request.ADDRESS = "/boards";
    }
    else if (to_list == LIST_POSTS)
    {
        request.ADDRESS.append("/board/").append(name);
    }

    request.METHOD = HTTP_GET;
    return request;
}

/**
 * Parse arguments and return required program values
 * 
 * @param argc Argument count
 * @param argv Arguments
 * @return target structure, which holds values about target and 
 */
target parse_arguments(int argc, char **argv)
{
    target target;
    std::string HOST;
    char c;

    while ((c = getopt(argc, argv, "p:H:")) != -1)
    {
        switch (c)
        {
        case 'p':
            target.port = std::atoi(optarg);
            break;
        case 'H':
            HOST = optarg;
            break;
        default:
            error("Unknown argument.");
        }
    }

    if (argv[optind] == nullptr)
    {
        error("[command] is a required argument. Please see -h for help.");
    }

    if (!strcmp(argv[optind], "boards"))
    {
        if (argc != 6)
        {
            error("Invalid arguments. Please see -h for help.");
        }
        target.request = create_GET_request(LIST_BOARDS, argv[optind]);
    }
    else if (!strcmp(argv[optind], "board"))
    {
        if (argc < 8)
        {
            error("Invalid arguments. Please see -h for help.");
        }
        if (!strcmp(argv[optind + 1], "list"))
        {
            target.request = create_GET_request(LIST_POSTS, argv[optind + 2]);
        }
        else if (!strcmp(argv[optind + 1], "add"))
        {
            target.request = create_POST_request(ADD_BOARD, argc, argv);
        }
        else if (!strcmp(argv[optind + 1], "delete"))
        {
            target.request = create_DELETE_request(DELETE_BOARD, argc, argv);
        }
    }
    else if (!strcmp(argv[optind], "item"))
    {
        if (argc < 8)
        {
            error("Invalid arguments. Please see -h for help.");
        }
        if (!strcmp(argv[optind + 1], "add"))
        {
            target.request = create_POST_request(ADD_POST, argc, argv);
        }
        else if (!strcmp(argv[optind + 1], "delete"))
        {
            target.request = create_DELETE_request(DELETE_POST, argc, argv);
        }
        else if (!strcmp(argv[optind + 1], "update"))
        {
            target.request = create_PUT_request(argc, argv);
        }
    }
    else
    {
        error("This [command] is not available. Please see -h for help.");
    }

    target.request.CONTENT_LENGTH = target.request.PAYLOAD.length();
    target.request.CONTENT_TYPE = HTTP_TEXT_PLAIN;
    target.request.VERSION = HTTP_VERSION;
    target.request.HOST = HOST;
    return target;
}

int main(int argc, char **argv)
{
    if (continue_program(argc, argv))
    {
        target target = parse_arguments(argc, argv);

        if (target.port > 0)
        {
            return send_request_receive_response(target) ? -1 : 0;
        }
    }
    return EXIT_SUCCESS;
}