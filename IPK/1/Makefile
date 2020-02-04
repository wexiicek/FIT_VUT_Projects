#!/bin/bash
build: 
	
run: ./xjurig00.py
ifndef api_key
	$(error api_key is not set)
else
ifndef city 
	$(error city is not set)
else
	@./xjurig00.py ${api_key} "${city}" ||:
endif
endif
