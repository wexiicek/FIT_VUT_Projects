#!/usr/bin/env python3

# Dominik Juriga (xjurig00) // ISJ Project 5

import bisect,re

checkArgs = object()

class Polynomial:
	def __init__(self, *arguments1, **arguments2):
		self.inputVals = []

		#Argument check***********
		if len(arguments1) == 0 and len(arguments2) > 0:	
			exponent = []
			base = []
			for everyDict in arguments2.items():
				#Correctivity check
				if isinstance(everyDict[0], str) == 0 or re.search(r'^x[0-9]+$',everyDict[0]) == 0 or isinstance(everyDict[1], int) == 0:	
					raise ValueError()
				else:
					#Adding values to the lists
					exponent.append(everyDict[0][1:])
					base.append(everyDict[1])	
			
			#Sorting based on exponent's value	
			exponent, base = (list(t) for t in zip(*sorted(zip(exponent, base))))			
	
			argList = []	
			#Counters
			i = 0	
			e = 0
			#If the exponent was in base list, adds it to the end of list and increases counter
			#Otherwise adds a zero
			while i <= int(exponent[-1]):
				if i == int(exponent[e]):
					argList.append(int(base[e]))
					e +=1
				else:
					argList.append(0)
				i += 1
		#Argument check***********
		elif len(arguments1) == 1 and len(arguments2) == 0 and isinstance(arguments1, tuple):
			argList = arguments1[0]
		#Argument check***********
		elif len(arguments1) > 0 and len(arguments2) == 0:
			argList = list(arguments1)
		
		#Argument check 
		for param in argList:	
			if isinstance(param, int) == False:
				raise ValueError()
		
		#Saves the data to instance of class
		self.inputVals = argList

	def __pow__(self, power):
		temporary = self.inputVals[:]	

		#Error check
		if power < 0:	
			raise ValueError()
		#Remains unchanched if power is one	
		if power == 1:	
			return self	
		#Returs zero if power is zero
		if power == 0:	
			return Polynomial([1])
		
		i = 1
		while i < power:	# Cycle for every iterration
			result = (len(temporary)+1)*[0]
			temp1 = 0
			temp2 = 0
			
			while temp1 < len(self.inputVals):
				while temp2 < len(temporary):
					#Performs temporary calculations of powers
					result[temp1+temp2] += self.inputVals[temp1] * temporary[temp2] 
					temp2 += 1
				temp1 += 1
				temp2 = 0
			#Put temporary result into a var
			temporary = result	
			#Increase counter
			i += 1
			
		#Return result
		return Polynomial(result)

	def at_value(self, value1, value2=checkArgs):
		
		#Argument check 
		if isinstance(value1, int) == 0 and isinstance(value1, float) == 0:	
			raise ValueError()

		exponent = 0	
		result1 = 0
		result2 = 0		
		
		#Counts the value of polynome
		for base in self.inputVals:
			result1 += base*(value1**exponent)
			exponent += 1
			
		#Returns first argument if second doesnt exist
		if value2 == checkArgs:
			return result1
		else: 
			#Argument check
			if isinstance(value2, int) == 0 and isinstance(value2, float) == 0:	
				raise ValueError()
			
			#Counts the value of polynome
			exponent = 0	
			for base in self.inputVals:
				result2 += base*(value2**exponent)	
				exponent += 1
			#Returns the diff between second and first result
			return result2-result1	
	
	#Equality check
	def __eq__(self, other):
		if str(other) == str(self):	
			return True
		else:
			return False		
			
	def __add__(self, other):
		#Finds the polynome with biggest exponent
		if len(self.inputVals) > len(other.inputVals):
			biggest = self.inputVals[:]
			smallest = other.inputVals[:]
		else:
			biggest = other.inputVals[:]
			smallest = self.inputVals[:]		
			
		i = 0		
		#Adds every shorter value to the longer
		#and returns the longer polynome
		while i < len(smallest):	
			biggest[i] += smallest[i]	
			i += 1
		return Polynomial(biggest)	
			
	def derivative(self):
		i = 0
		result = [0]*(len(self.inputVals)-1)
		
		if result == []:
			return 0
		#Counts derivated value and
		#returns derivated polynome
		for value in self.inputVals:	
			result[i-1] = value*i	
			i += 1
		return Polynomial(result)

	def __str__(self):
		result = ""	
		#Highest value of index(-1) is equal to exponent
		index = len(self.inputVals)-1
		#Goes through all the bases
		for base in self.inputVals[::-1]:
			#Checks base cases
			if base == 0:
				index -= 1
				continue
			#Checks base cases // -1
			if base == -1 and index != 0:	
				base = " - "
			#Checks base cases // negative number
			elif base < 0:	
				base = " - {:d}".format(abs(base))
			#Checks base cases // +1
			elif base == 1 and index != 0:	
				base = " + "
			#Checks base cases // positive number
			else:	
				base = " + {:d}".format(base)
			#Anything to the power of 0 is 1, therefore empty
			if index == 0:	
				exponent = ""
			#Anything to the power of 1 is same, therefore same
			elif index == 1:
				exponent = "x"
			#Other cases
			else:
				exponent = "x^{:d}".format(index)
			
			#Adds the result of calculation
			result = result + "{:s}{:s}".format(base,exponent)	
			index = index - 1
			
		#Remove characters if number is negative
		if result[:2] == " -":
			return result[1:]
		#Remove characters if number is positive
		elif result[:2] == " +":	
			return result[3:]	
		#Return zero if empty
		elif result == "":	
			return "0"	
		#returns the result
		return result	

def test():
    assert str(Polynomial(0,1,0,-1,4,-2,0,1,3,0)) == "3x^8 + x^7 - 2x^5 + 4x^4 - x^3 + x"
    assert str(Polynomial([-5,1,0,-1,4,-2,0,1,3,0])) == "3x^8 + x^7 - 2x^5 + 4x^4 - x^3 + x - 5"
    assert str(Polynomial(x7=1, x4=4, x8=3, x9=0, x0=0, x5=-2, x3= -1, x1=1)) == "3x^8 + x^7 - 2x^5 + 4x^4 - x^3 + x"
    assert str(Polynomial(x2=0)) == "0"
    assert str(Polynomial(x0=0)) == "0"
    assert Polynomial(x0=2, x1=0, x3=0, x2=3) == Polynomial(2,0,3)
    assert Polynomial(x2=0) == Polynomial(x0=0)
    assert str(Polynomial(x0=1)+Polynomial(x1=1)) == "x + 1"
    assert str(Polynomial([-1,1,1,0])+Polynomial(1,-1,1)) == "2x^2"
    pol1 = Polynomial(x2=3, x0=1)
    pol2 = Polynomial(x1=1, x3=0)
    assert str(pol1+pol2) == "3x^2 + x + 1"
    assert str(pol1+pol2) == "3x^2 + x + 1"
    assert str(Polynomial(x0=-1,x1=1)**1) == "x - 1"
    assert str(Polynomial(x0=-1,x1=1)**2) == "x^2 - 2x + 1"
    pol3 = Polynomial(x0=-1,x1=1)
    assert str(pol3**4) == "x^4 - 4x^3 + 6x^2 - 4x + 1"
    assert str(pol3**4) == "x^4 - 4x^3 + 6x^2 - 4x + 1"
    assert str(Polynomial(x0=2).derivative()) == "0"
    assert str(Polynomial(x3=2,x1=3,x0=2).derivative()) == "6x^2 + 3"
    assert str(Polynomial(x3=2,x1=3,x0=2).derivative().derivative()) == "12x"
    pol4 = Polynomial(x3=2,x1=3,x0=2)
    assert str(pol4.derivative()) == "6x^2 + 3"
    assert str(pol4.derivative()) == "6x^2 + 3"
    assert Polynomial(-2,3,4,-5).at_value(0) == -2
    assert Polynomial(x2=3, x0=-1, x1=-2).at_value(3) == 20
    assert Polynomial(x2=3, x0=-1, x1=-2).at_value(3,5) == 44
    pol5 = Polynomial([1,0,-2])
    assert pol5.at_value(-2.4) == -10.52
    assert pol5.at_value(-2.4) == -10.52
    assert pol5.at_value(-1,3.6) == -23.92
    assert pol5.at_value(-1,3.6) == -23.92

if __name__ == '__main__':
    test()