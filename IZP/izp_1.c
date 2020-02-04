#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>

#define MAX_SIZE 101

int upper_trans(char str1[], char str2[]){

    /*  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
    *      converts str1(argv 1) and str2(address from .txt file)    *
    *      into uppercase, for easy comparison and eliminating       *
    *      possible mistakes during comparison                       *
    *      > uses toupper function;                                  *
    *   **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  */

    int i, length;
    length = strlen(str1);
    for (i = 0; i < length; i++){
        if(isalpha(str1[i])){
            str1[i] = toupper(str1[i]);
        }
    }
    length = strlen(str2);
    for (i = 0; i < length; i++){
        if(isalpha(str2[i])) {}
        str2[i] = toupper(str2[i]);
    }
    return 0;
}

int arg_compare(char str1[], char str2[], int *found,  char output[], char enabled[]){
    /*  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
     *  compares user's argument with entry from .txt address file           *
     *  compares each char individually, if the whole argument               *
     *  matches prefix                                                       *
     *      > returns next char as enabled                                   *
     *  if it does not match                                                 *
     *      >breaks                                                          *
     *  also: increases the found variable, which determines the output      *
     *  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  */

    int i, len, correct = 0;
    len = strlen(str2);
    for (i = 0; i < len; i++){
        if (str1[i] == str2[i]){
            correct += 1;
            if (correct == len){
                enabled[*found] = str1[i+1];
                *found += 1;
                strcpy(output, str1);
            }
        }
        else if (str1[i] != str2[i])
            break;
    }
    return 0;
}

int remove_duplicates(char str[]){
    /*  **  **  **  **  **  **  **  **  **  **  **  **  **
     *  removes duplicates by using nested for loops     *
     *  for comparing individual characters              *
     *  changes position of character if its bigger than *
     *  the next one.. in the end it is replaced with \0 *
     *  thus removing excessive duplicated characters    *
     *  **  **  **  **  **  **  **  **  **  **  **  **  */

    int i,j,len1,len2;
    len1 = strlen(str);
    len2=0;
    for(i=0; i<(len1-len2);){
        if(str[i]==str[i+1]){
            for(j=i; j<(len1-len2); j++)
                str[j]=str[j+1];
            len2++;
        }
        else{
            i++;
        }
    }
    return 0;
}


int sort_array(char str1[]){
    /*  **  **  **  **  **  **  **  **  **  **  **  **  **
     *  sorts the 'enabled' array, compares chars        *
     *  and switches their position if the condition     *
     *  is != 0, uses temporary variable to store        *
     *  one of the chars while changing their place      *
     *  **  **  **  **  **  **  **  **  **  **  **  **  */

    int i, j, length = 0;
    char temporary;
    length = strlen(str1);
    for (i = 0 ; i < ( length - 1 ); i++){
        for (j = 0 ; j <(length - i - 1); j++){
            if (str1[j] > str1[j+1]){
                temporary = str1[j];
                str1[j] = str1[j+1];
                str1[j+1] = temporary;
            }
        }
    }
    return 0;
}

int print_result(char enabledChars[], int found, char output[], int argcount){
    /*  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
     *  prints the output of the program according to the previous functions:    *
     *  if one match has been found                                              *
     *      > print 'Found: city'                                                *
     *  if more cities have been found                                           *
     *      > print 'Enabled: characters'                                        *
     *  if no city is found                                                      *
     *      > print 'Not found'                                                  *
     *  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  */

    if (found == 1){
        printf("Found: %s\n", output);
    }
    else if (found > 1){
            printf("Enable: %s\n", enabledChars);
    }
    else if (found == 0){
        if (argcount == 2)
            printf("Not found\n");
    }
    return 0;
}

int main(int argc, char * argv[]){
    /*  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
     *  loads the argument and stores it into a array of chars       *
     *  loads the 1 whole line from the .txt file                    *
     *  calls functions and counts found cities                      *
     *  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  */
    char addressList[MAX_SIZE][MAX_SIZE], enabledChars[MAX_SIZE];
    char output[MAX_SIZE], argument[MAX_SIZE];
    int counter = 0, found = 0, correct, i, argcount = argc, len;
    (void) correct;

    if ((counter == 0) && (scanf(" %[^\n]", addressList[0]) == EOF)){
        fprintf(stderr, "1/ File is empty\n");   
        return 1;
    }
    else{
        len = strlen(addressList[0]);       // if file is not empty, assign the first
        for(i = 100; i <= len; i++){        // loaded line to the first position of array 
            addressList[0][i] = '\0';       // replace excessive characters over 100 with \0
        }
        counter++;
    }
    while (scanf(" %[^\n]", addressList[counter]) != EOF){
        len = strlen(addressList[counter]);
        for(i = 100; i <= len; i++){
            addressList[counter][i] = '\0';     // replace excessive chars with \0
        }
            counter++;        
    }
    if(argc == 1){      // if there is no user argument, program outputs first letter of each entry
        for(i = 0; i <= counter; i++){
            enabledChars[i] = addressList[i][0];
        }
        upper_trans(enabledChars, argument);
        sort_array(enabledChars);
        remove_duplicates(enabledChars);
        printf("Enable: %s\n", enabledChars);
    }
    else if (argc == 2){        // if there is one user argument, program outputs enabled chars
        strcpy(argument, argv[1]);  // based on the arg_compare function
        for (i = 0; i <= counter; i++){
            upper_trans(addressList[i], argument);
            arg_compare(addressList[i], argument, &found, output, enabledChars);
        }
    }
    else{
        fprintf(stderr, "2/ Invalid input. Way too many arguments.\n");
        return 2;
    }
    if (found > 1)
        sort_array(enabledChars);
    remove_duplicates(enabledChars);
    print_result(enabledChars, found, output, argcount);
    return 0;
}
