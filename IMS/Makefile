all: ims

ims:
	g++ -o ims ims.cpp

run:
	./ims -solardata solarData.txt -condata consumptionData.txt -count 1554794 -consumption 15050300000 -size 2.28

run-custom:
	./ims $(args)

clear:
	rm ims
