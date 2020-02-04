CC=g++

CFLAGS=-std=c++11 -Wall -Wextra -pedantic -g

SERVER=isaserver
CLIENT=isaclient

all: $(SERVER) $(CLIENT)

$(SERVER).o: $(SERVER).cpp http_message.hpp board.hpp
	$(CC) $(CFLAGS) -c $(SERVER).cpp -o $(SERVER).o

$(SERVER): $(SERVER).o
	$(CC) $(CFLAGS) $(SERVER).o -o $(SERVER)

$(CLIENT).o: $(CLIENT).cpp http_message.hpp target.hpp
	$(CC) $(CFLAGS) -c $(CLIENT).cpp -o $(CLIENT).o

$(CLIENT): $(CLIENT).o
	$(CC) $(CFLAGS) $(CLIENT).o -o $(CLIENT)

clean:
	rm -f $(SERVER) $(CLIENT) *.o

tar:
	tar -cvf xjurig00.tar *.cpp *.hpp manual.pdf README Makefile
