#!/usr/bin/env python3

# Dominik Juriga (xjurig00) // ISJ Project 6

import itertools
            
def first_nonrepeating(S):
    """function returns the first non-repeating character of a string
    input is: string
    output is: first non repeating character or None
    """
    if (isinstance(S, str)):
    	if not S.isspace():
    		for CH in S:
    			if (S.count(CH) == 1):
    				return CH
    return None

def combine4(NUMBERS, RESULT):
    """
returns combinations of OPS between numbers to get
RESULT
input is:
list for NUMBERS
integer for RESULT
output is:
combinations of numbers and operators in list
    """

	#list of operators
    OPS = ["+", "-", "*", "/"]
    
    #list of all possible equations
    EQUATIONS = []
    
    #temporary list for duplicates
    EQUATIONS2 = []

    #list for all equations that meet the critereia
    RESULT_FINAL = []
    
    #single opening bracket
    SINGLE_O="("
    
    #single closing bracket
    SINGLE_C=")"
    
    #double opening bracket
    DOUBLE_O="(("
    
    #double closing bracket
    DOUBLE_C="))"

    #number count
    COUNT = 4

    for PERMUTATION in itertools.permutations(NUMBERS, 4):
        #first operator
        for i in range(COUNT):
            #second operator
            for j in range(COUNT):
                #third operator
                for k in range(COUNT):

                    #first two in brackets
                    EQUATIONS.append(SINGLE_O + str(PERMUTATION[0]) + OPS[i] + str(PERMUTATION[1]) + SINGLE_C +
                                    OPS[j] + str(PERMUTATION[2]) + OPS[k] + str(PERMUTATION[3]))

                    #middle two in brackets
                    EQUATIONS.append(str(PERMUTATION[0]) + OPS[i] + SINGLE_O + str(PERMUTATION[1]) + OPS[j]
                                    + str(PERMUTATION[2]) + SINGLE_C + OPS[k] + str(PERMUTATION[3]))

                    #last two in brackets
                    EQUATIONS.append(str(PERMUTATION[0]) + OPS[i] + str(PERMUTATION[1]) + OPS[j] + SINGLE_O
                                    + str(PERMUTATION[2]) + OPS[k] + str(PERMUTATION[3]) + SINGLE_C)
                    
                    #first two and last two in brackets
                    EQUATIONS.append(SINGLE_O + str(PERMUTATION[0]) + OPS[i] + str(PERMUTATION[1]) + SINGLE_C +
                                    OPS[j] + SINGLE_O + str(PERMUTATION[2]) + OPS[k] + str(PERMUTATION[3]) + SINGLE_C)
                    
                    #first two in brackets with third in brackets
                    EQUATIONS.append(DOUBLE_O + str(PERMUTATION[0]) + OPS[i] + str(PERMUTATION[1]) + SINGLE_C + OPS[j] + str(
                        PERMUTATION[2]) + SINGLE_C + OPS[k] + str(PERMUTATION[3]))
                    
                    #last two in brackets with second in bracket
                    EQUATIONS.append(str(PERMUTATION[0]) + OPS[i] + SINGLE_O + str(PERMUTATION[1]) + OPS[j] + SINGLE_O + str(
                        PERMUTATION[2]) + OPS[k] + str(PERMUTATION[3]) + DOUBLE_C)
                    
                    #middle two in brackets with last in brackets
                    EQUATIONS.append(str(PERMUTATION[0]) + OPS[i]+ DOUBLE_O + str(PERMUTATION[1]) + OPS[j] + str(
                        PERMUTATION[2]) + SINGLE_C + OPS[k] + str(PERMUTATION[3]) + SINGLE_C)
                    
                    #middle two in brackets with first in brackket
                    EQUATIONS.append(SINGLE_O + str(PERMUTATION[0]) + OPS[i] + SINGLE_O + str(PERMUTATION[1]) + OPS[j] + str(
                        PERMUTATION[2]) + DOUBLE_C + OPS[k] + str(PERMUTATION[3]))
                    
					#last three in brackets
                    EQUATIONS.append(str(PERMUTATION[0]) + OPS[i] + SINGLE_O + str(PERMUTATION[1]) + OPS[j] + str(
                        PERMUTATION[2]) + OPS[k] + str(PERMUTATION[3]) + SINGLE_C) 

                    #first three in brackets
                    EQUATIONS.append(SINGLE_O + str(PERMUTATION[0]) + OPS[i] + str(PERMUTATION[1]) + OPS[j] + str(
                        PERMUTATION[2]) + SINGLE_C + OPS[k] + str(PERMUTATION[3]))
                     
                    #numbers without any brackets with operators
                    EQUATIONS.append(str(PERMUTATION[0]) + OPS[i] + str(PERMUTATION[1]) + OPS[j] +
                                    str(PERMUTATION[2]) + OPS[k] + str(PERMUTATION[3]))

    
    for INSTANCE in range(EQUATIONS.__len__()):
    	#evaluates the equations and compares with the result
        try:
            if eval(EQUATIONS[INSTANCE]) == RESULT:
                EQUATIONS2.append(EQUATIONS[INSTANCE])
        except ZeroDivisionError:
            pass


    for INSTANCE in EQUATIONS2:
    	#removes duplicates by comparing and appending if it's not already there
        if INSTANCE not in RESULT_FINAL:
            RESULT_FINAL.append(INSTANCE)

    if (RESULT_FINAL == 0):
        return None
    return RESULT_FINAL