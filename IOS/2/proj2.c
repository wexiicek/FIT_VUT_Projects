/*
	IOS Project 2
	Dominik Juriga (xjurig00)
	1BIT 2017/2018
*/

// LIBRARIES
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/wait.h>
#include <sys/types.h>
#include <semaphore.h>
#include <fcntl.h>
#include <sys/shm.h>
#include <signal.h>
#include <ctype.h>
#include <string.h>
#include <time.h>

//############################################//

// DEFINITIONS & SEMAPHORS
#define ERR 1 //Error value

#define IGNORE(var) do { (void)(var); } while (0) //Supress Unused Variable warning

//############################################//

#define semaphore1 "/xjurig00.proj2.semaphore1"
#define semaphore2 "/xjurig00.proj2.semaphore2"
#define semaphore3 "/xjurig00.proj2.semaphore3"
#define semaphore4 "/xjurig00.proj2.semaphore4"

//############################################//

// VARIABLES & SEMAPHORE INICIALIZATION
sem_t *mutex = NULL;
sem_t *bus = NULL;
sem_t *boarded = NULL;
sem_t *switchSem = NULL;

int R, C, ART, ABT;
//-----
int *sharedCounter = NULL;
int sharedCounterID = 0;
//-----
int *finished = NULL;
int finishedID = 1;
//-----
int *counter = NULL;
int counterID = 2;
//-----
int *waiting = NULL;
int waitingID = 3;
//-----
FILE *file;
//############################################//

// PROTOTYPES
void clearResources();

//############################################//

int printError(int choice){
	
	/*
		Function that handles error messages, clears any 
		allocated resources and quits the program

		Input: error value
		Output: nothing
	*/
	
	switch(choice){
		case 1:
			fprintf(stderr, "Invalid arguments. [count]\n");
			break;
		case 2:
			fprintf(stderr, "Invalid arguments. [type or value]\n");
			break;
		case 3:
			fprintf(stderr, "An error has occured.\n");
			break;
		case 4:
			fprintf(stderr, "Cannot access file.\n");
			break;
		case 5:
			fprintf(stderr, "Error while creating process.\n");
			break;
		case 6:
			fprintf(stderr, "Error while closing file.\n");
			break;
		case 7:
			fprintf(stderr, "Error while creating semaphors.\n");
			break;
		case 8:
			fprintf(stderr, "Error while creating shared memory.\n");
			break;
	}
	clearResources();
	exit(ERR);
	return 0;
}

void argToVar(char*argv[]){
	
	/*
		Function that transforms arguments into variables
		Input: argument array
		Output: Variables with correct values
	*/
	
	R = atoi(argv[1]);
	C = atoi(argv[2]);
	ART = atoi(argv[3]);
	ABT = atoi(argv[4]);
}

int argCheck(int argc, char*argv[]){
	
	/*
		Function that checks the validity of arguments
		Input: argument count & argument array
		Output: invalid value -> 1
				valid value   -> 0
	*/
	
	if(argc != 5)
		printError(1); // invalid argument count

	//check every character of every argument
	for ( int i = 1; i < argc; i++ ){
		char *temp = argv[i];
		//check if digit
		for ( unsigned int j = 0; j < strlen(temp); j++ ) {
			if(!isdigit(temp[j]))
				printError(2); // if not digit -> invalid argument type
		}
	}

	argToVar(argv);

	if (R < 0 || C < 0 || ART < 0 || ART > 1000 || ABT < 0 || ABT > 1000)
		printError(2); // invalid argument value(s)
	return 0;
}

int generateRandomTime(int maxTime) {
	
	/*
		Function that generates random time 
		Input: maximal time value
		Output: Random time from <0 - maxTime>
	*/

	return (rand() % (maxTime + 1));
}

void createRiders(int riderTime, int R) {

	/*
    	Function that handles rider process
    	Based on solution from the book
    	Little Book of Semaphores
    	Input: maximal rider time and rider count
    	Output: Handles rider process
    */

    for(int i = 0; i < R; i++) {
    	//create rider process
        int pidR = fork();
        if(pidR < 0)
            printError(5);
        else if (pidR == 0) {
        	sem_wait(switchSem);
            fprintf(file,"%d:	RID %d		: start\n", ++(*counter), i + 1);
            sem_post(switchSem);            
            sem_wait(mutex);
            (*waiting)++;
            sem_wait(switchSem);
            fprintf(file,"%d:	RID %d		: enter: %d\n", ++(*counter), i + 1, *waiting);
            sem_post(switchSem);
            sem_post(mutex);            
            sem_wait(bus);
            sem_wait(switchSem);
            if (*finished > 0){
            	for (int j = *finished; j > 0; j--)
            		fprintf(file, "%d:	RID %d		: finish\n", ++(*counter), i - j + 1);
            	*finished = 0;
            }
            fprintf(file,"%d:	RID %d		: boarding\n", ++(*counter), i + 1);
            sem_post(switchSem);
            (*sharedCounter)++;
            sem_post(boarded);
            exit(0);            
        }
        else
            usleep(riderTime*1000);
    }
}
    


void handleBus(int R, int C, int busTime) {

	/*
		Function that handles the bus process
    	Based on solution from the book
    	Little Book of Semaphores
		Input: rider count, bus capacity and maximal bus time
		Output: Handles bus process
	*/

	while (*sharedCounter != R) {
		sem_wait(mutex);
		sem_wait(switchSem);
		fprintf(file,"%d:	BUS		: arrival\n", ++(*counter));
		sem_post(switchSem);
		
		int amount = 0;
		if (*waiting <= C)
			amount = *waiting;
		else 
			amount = C;

		if (*waiting > 0){
			fprintf(file,"%d:	BUS		: start boarding: %d\n", ++(*counter), *waiting);
			for (int i = 0; i < amount; i++){
				sem_post(bus);
				sem_wait(boarded);
			}
			*waiting = *waiting - amount;
			*finished = amount;
			fprintf(file,"%d:	BUS		: end boarding: %d\n", ++(*counter), *waiting);
		}

		sem_post(mutex);
		sem_wait(switchSem);
		fprintf(file,"%d:	BUS		: depart\n", ++(*counter));
		sem_post(switchSem);
		usleep(busTime*1000);
		fprintf(file,"%d:	BUS		: end\n", ++(*counter));
    }
    sem_wait(switchSem);
    fprintf(file,"%d:	BUS		: finish\n", ++(*counter));
    int rCounter = (*sharedCounter - *finished + 1);
    for (int i = 0; i < *finished; i++) {
        fprintf(file, "%d:	RID %d		: finish\n", ++(*counter), rCounter + i);
    }
    sem_post(switchSem);
}

int createSemaphors(){

	/*
		Function that initializes semaphors
		Input: none
		Output: semaphors are created - > 0
				error -> 1
	*/

	if ( ((mutex = sem_open(semaphore1, O_CREAT | O_EXCL, 0666, 1)) == SEM_FAILED)
	   || ((bus = sem_open(semaphore2, O_CREAT | O_EXCL, 0666, 0)) == SEM_FAILED)
	   || ((boarded = sem_open(semaphore3, O_CREAT | O_EXCL, 0666, 0)) == SEM_FAILED)
	   || ((switchSem = sem_open(semaphore4, O_CREAT | O_EXCL, 0666, 1)) == SEM_FAILED) )
		return 1;
	return 0;
}

void clearResources(){

	/*
		Function that closes and unlinks semaphors and
		clears shared memory
		Input: none
		Output: semaphors are unlinked and shared memory is cleared
	*/

	//Close all present semaphors
   	sem_close(switchSem);
   	sem_close(boarded);
    sem_close(bus);
	sem_close(mutex);

	//Unlink all present semaphors
	sem_unlink(semaphore4);
    sem_unlink(semaphore3);
    sem_unlink(semaphore2);
	sem_unlink(semaphore1);
   
   	//Clear all allocated shared memory variables
	shmctl(counterID, IPC_RMID, NULL);
    shmctl(waitingID, IPC_RMID, NULL);
    shmctl(sharedCounterID, IPC_RMID, NULL);
    shmctl(finishedID, IPC_RMID, NULL);
}

int createSharedMemory(){

	/*
		Function that creates shared memory
		Input: none
		Output: shared memory is created -> 0
				error -> 1
	*/

	if (((sharedCounterID = shmget(IPC_PRIVATE, sizeof(int), IPC_CREAT | 0666) ) == -1 ) || ((sharedCounter = shmat(sharedCounterID, NULL, 0)) == NULL))
		return 1;
	*sharedCounter = 0;
	if (((waitingID = shmget(IPC_PRIVATE, sizeof(int), IPC_CREAT | 0666)) == -1) || ((waiting = shmat(waitingID, NULL, 0)) == NULL))
		return 1;
	*waiting = 0;	
	if (((counterID = shmget(IPC_PRIVATE, sizeof(int), IPC_CREAT | 0666)) == -1) || ((counter = shmat(counterID, NULL, 0)) == NULL))
		return 1;
	*counter = 0;
	if (((finishedID = shmget(IPC_PRIVATE, sizeof(int), IPC_CREAT | 0666)) == -1) || ((finished = shmat(finishedID, NULL, 0)) == NULL))
		return 1;
	*finished = 0;
	
	return 0;
}

int main(int argc, char *argv[]){
	//initiate rand function with time as SEED
	srand(time(NULL));

	//check arguments - count, type, values
	argCheck(argc, argv);
	
//creating shared memory
	//if 1 is returned, calls error and quits the program
	if(createSharedMemory())
		printError(8);	

	//creating semaphors
	//if 1 is returned, calls error and quits the program
	if(createSemaphors())
		printError(7);
	
	
	
	//generate maximal rider time
	int riderTime = generateRandomTime(ART);
	//generate maximal bus time
	int busTime = generateRandomTime(ABT);
	
	//supressing unused var warning
	IGNORE(riderTime);
	IGNORE(busTime);
	//---------------------------

	//file opening check
	//if NULL is returned, calls error and quits the program
	if ((file = fopen("proj2.out", "w")) == NULL)
		printError(4);
	//prevent file buffering
	setbuf(file, NULL);

	//create two sub processes
	//bus and riders are created further on
	for(int i = 0; i <= 1; i++) {
		int pidM = fork();
	    if(pidM == 0){
	    	if (i == 0){
	    		fprintf(file,"%d:	BUS		: start\n", ++(*counter));
	    		handleBus(R, C, riderTime);
	    	}
	    	if (i == 1)
	    		createRiders(riderTime, R);
	    	exit(0);
	    }
	}

	//wait for child processes to terminate
	for(int i = 0; i <= 1; i++)
	    wait(NULL);
	 
	//cleaning the resources 
	//clearing shared memory and closing, unlinking semaphors
	clearResources();
	//checks file closing
	//if EOF, calls error and quits the program 
	if (fclose(file) == EOF)
		printError(6);

	return 0;
}
