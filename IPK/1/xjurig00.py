#!/usr/bin/env python3

from socket import *
#used for implementing a socket for connecting to the openweather API
from sys import *
#used for retrieving arguments from client call
import json
#used for sorting API weather info

if (len(argv)) != 3:
	exit("Invalid argument count.")
#Check if there is a correct argument cound

if len(argv[1])==0 or len(argv[2])==0:
	exit("Invalid arguments.")

if(len(argv[1]) != 32):
	exit("Invalid API key or arg format.")
#Check if the api_key argument is in correct format

def urlBuild(cityName, unit, API_key):
	url = '/data/2.5/weather?q={0}&mode=json&units={1}&APPID={2}'.format(str(cityName), unit, API_key)
	return url
#Build a complete URL for API request with optional city name, unit and API key
#Returns URL

def reqBuild(URL, host):
	#req = 'GET ' + URL + ' HTTP/1.1\r\nHost: ' + host + '\r\nConnection: close\r\n\r\n'
	req = 'GET {0} HTTP/1.1\r\nHost: {1}\r\nConnection: close\r\n\r\n'.format(URL, host)
	return req
#Build a complete HTTP GET request for OpenWeather API.
#Returns GET request

def checkResponse(data):
	retCode = data[data.find('HTTP/1.1',0,len(data)) + len('HTTP/1.1 '):data.find('HTTP/1.1',0,len(data)) + len('HTTP/1.1 ') + 3]
	#Stripping data.. Cut everything before "HTTP/1.1 " an after "HTTP/1.1 " + 3 characters (3 chars == HTTP response code) 
	if int(retCode) != 200:
		exit("Invalid response from API. ({})".format(retCode))
#Upon receiving response from the API, I perform a check of HTTP response.
#If the response is 200 OK, the program continue
#Otherwise the program ends with error response containing HTTP error value.

API_key = argv[1]
#Retrieve API key from program call argument and strip the "api_key=" so that I am left with 32 char API key

CITY = argv[2].lower()
#Retrieve city name from the program call argument
#There is no need to perfor m checks, since it has to correct or API will return 404
#The input is converted to lowercase, because the API doesnt work with some names
#e.g. it will give results for city="Uherske hradiste" but not for city="Uherske Hradiste"

UNIT = 'metric' #Display results in metric units

HOST = 'api.openweathermap.org' #API host address

PORT = 80 #API operating port

URL = urlBuild(CITY,UNIT,API_key) #Build a URL for GET request

REQUEST = reqBuild(URL, HOST) #Build GET request based on user inputs

with socket(AF_INET, SOCK_STREAM) as s:
	try:
		s.connect((HOST, PORT)) #Attempt to connect to the API
		s.sendall(bytes((REQUEST.encode()))) #Send user request to the API
		httpData = s.recv(2048).decode('utf-8') #Retrieve weather data from the API with UTF-8 encoding
		s.close() #Close the socket after downloading data
		RET = httpData #Convert the data to string
		checkResponse(RET) #Check the response code (200 OK = Continue, exit otherwise)
		(header, body) = RET.split("\r\n\r\n") #If the response was OK, strip the HTTP header
	except Exception as ex:
		exit("Error in communication with server. ({})".format(ex)) #If any command raised an expection, exit the program

jData = json.loads(body) #Load the data into a json using json library

if 'name' in jData:
	print ('{}'.format(jData['name']))
if 'weather' in jData:
	if 'description' in jData['weather'][0]:
		print ('{}'.format(jData['weather'][0]['description']))
	else:
		print ('{}'.format('N/A'))
if 'main' in jData:
	if 'temp' in jData['main']:
		print ('temp: {}°C'.format(jData['main']['temp']))
	if 'humidity' in jData['main']:
		print ('humidity: {}%'.format(jData['main']['humidity']))
	else:
		print ('humidity: {}%'.format('N/A'))
	if 'pressure' in jData['main']:
		print ('pressure: {}hPa'.format(jData['main']['pressure']))
	else:
		print ('pressure: {}hPa'.format('N/A'))
if 'wind' in jData:
	if 'speed' in jData['wind']:
		print ('wind-speed: {0:.2f}km/h'.format(jData['wind']['speed']*(3.6)))
	else:
		print ('wind-speed: {}km/h'.format('N/A'))
	if 'deg' in jData['wind']:
		print ('wind-deg: {}'.format(jData['wind']['deg']))
	else:
		print ('wind-deg: {}'.format('N/A'))
#This block is used to check for certain items in json 
#If the item is present, it is printed to the output
#Otherwise we print n/a where applicable
