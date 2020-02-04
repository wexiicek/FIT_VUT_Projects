#!/usr/bin/env python3

#ISJ Project 7 // Dominik Juriga (xjurig00)

import math
		
class TooManyCallsError(Exception):
	"""
	Class for raising too many calls exception
	"""
	def __init__(self, message):
		"""
		message = error message
		"""
		self.message = message

def limit_calls(max_calls=2, error_message_tail="called too often"):
    """
        Decorator for calling functions with limited call count.
    """
    def limit_calls(func):
        def wrapper(a, b):
            if max_calls > wrapper.calls:
                wrapper.calls += 1
                return func(a, b)
            else:
                errMsg = 'function "'+func.__name__+'" - '+str(error_message_tail)
                raise TooManyCallsError(errMsg)
        wrapper.calls = 0
        return wrapper
    return limit_calls


@limit_calls(1, 'that is too much')
def pyth(a, b):
    """
        Pythagoras' theorem
    """
    c = math.sqrt(a**2 + b ** 2)
    return c


def ordered_merge(*args, selector = []):
	"""
		Loops through args and selects items based on selector value.
	"""
	
	result = []
	try:
		for item in args:
			result.append(iter(item))
	except TypeError:
		raise Exception("Invalid input\n")

	for i in selector:
	    yield(next(result[i]))


class Log():
    """
        Class that writes into file
    """
    def __init__(self, file):
        """
            File name initialization
        """
        self.file = file

    def __enter__(self):
        """
            File opening and writing first line
        """
        self.f = open(self.file, 'w')
        self.f.write('Begin\n')
        return self

    def logging(self, text):
        """
            Writing into file
        """
        self.f.write(text + "\n")
        
    def __exit__(self, exc_type, exc_val, exc_tb):
        """
            Writing ending line and closing file
        """
        self.f.write('End\n')
        self.f.close()
