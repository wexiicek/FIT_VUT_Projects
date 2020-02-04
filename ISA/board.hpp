#include <string>
#include <vector>
#include <list>

struct post
{
    int id;
    std::string text;
};

struct board
{
    std::string name;
    std::list<post> posts;
    int last_id = 1;
};