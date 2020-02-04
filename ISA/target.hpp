#include "http_message.hpp"

#define LIST_BOARDS 999
#define LIST_POSTS 9999
#define ADD_BOARD 888
#define ADD_POST 8888
#define DELETE_BOARD 777
#define DELETE_POST 7777

struct target
{
    int port;
    http_msg request;
};