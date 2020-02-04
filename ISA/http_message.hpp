#include <string>

//HTTP methods
#define HTTP_GET "GET"
#define HTTP_POST "POST"
#define HTTP_PUT "PUT"
#define HTTP_DELETE "DELETE"

//Random HTTP
#define HTTP_VERSION "HTTP/1.1"
#define HTTP_TEXT_PLAIN "text/plain"

//HTTP Header Flags
#define HTTP_CONTENT_TYPE "Content-Type:"
#define HTTP_CONTENT_LENGTH "Content-Length:"
#define HTTP_HOST "Host:"
#define HTTP_LINE_END "\r\n"

//HTTP response codes
#define HTTP_OK 200
#define HTTP_CREATED 201
#define HTTP_BAD_REQUEST 400
#define HTTP_NOT_FOUND 404
#define HTTP_CONFLICT 409

//HTTP response captions
#define HTTP_OK_T "OK"
#define HTTP_CREATED_T "Created"
#define HTTP_BAD_REQUEST_T "Bad Request"
#define HTTP_NOT_FOUND_T "Not Found"
#define HTTP_CONFLICT_T "Conflict"

struct http_msg
{
    std::string METHOD = "";
    std::string ADDRESS = "";
    std::string VERSION = "";
    std::string HOST = "";
    std::string CONTENT_TYPE = "";
    std::string PAYLOAD;
    std::string RESPONSE_TEXT = "";
    int CONTENT_LENGTH;
    int RESPONSE;
};