// IMS 2019 - Projekt - Uhlíková stopa v energetice a teplárenství
// Adam Linka (xlinka01), Dominik Juriga (xjurig00)
// FIT VUT 2019/2020


#include <sstream>
#include <string>
#include <fstream>
#include <iostream>
#include <vector>
#include <list>
#include <iomanip>
#include <getopt.h>
#include <math.h>

using namespace std;

string months [] = {"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"};

int getWorstMonth(std::vector<float> solarData, std::vector<float> consumptionData) {
	float lowestCoverage = 2;
	int worstMonth;
	for (int i = 0; i < 12; i++)
	{
		if ((solarData[i] * consumptionData[i]) < lowestCoverage)
		{
			lowestCoverage = (solarData[i] * consumptionData[i]);
			worstMonth = i;
		}
	}
	return worstMonth;
}


void calculatePercentage (std::vector<float> solarData, std::vector<float> consumptionData, float solarSize, float solarCount, float consumption)
{
	cout << solarCount << " PVEs with power output of " << solarSize << " kWp each will cover power consumption of " << consumption << " kWh like this:" << endl;
	cout << endl;
	cout.width(20); cout << left << "Month";
	cout.width(30); cout << left << "Estimated solar coverage";
	cout.width(20); cout << left << "To be covered by other sources"  << endl;
	cout << "--------------------------------------------------------------------------------" << endl;
	for (int i = 0; i < 12; i++)
	{
		float result = ((solarSize * solarData[12] * solarCount * solarData[i]) / (consumption * consumptionData[i]));
		float otherSources = (1 - result) * (consumption * consumptionData[i]);
		cout.width(20); cout << left << months[i]; 
		cout.width(7); cout << left << result*100;
		cout.width(23); cout << " %";
		if (otherSources > 0)
		{
			cout.width(7); cout << left << otherSources << " kWh" << endl;
		}
		else
		{
			cout.width(7); cout << left << "-" << endl;
		}
		
	}
}

void calculateOutput (std::vector<float> solarData, std::vector<float> consumptionData, float solarCount, float percentage, float consumption)
{
	int worstMonth = getWorstMonth(solarData, consumptionData);

	float result = ((consumption * consumptionData[worstMonth])/(solarData[12] * solarCount * solarData[worstMonth]))*percentage;

	cout << "To reach a minimum coverage of " << percentage*100 << " %, PVEs of aproximately " << result << " kWp will be needed." << endl;

	calculatePercentage(solarData, consumptionData, result, solarCount, consumption);
}


void calculateCount (std::vector<float> solarData, std::vector<float> consumptionData, float solarSize, float percentage, float consumption)
{
	int worstMonth = getWorstMonth(solarData, consumptionData);

	float result = ceil((consumption * consumptionData[worstMonth] * percentage)/(solarData[12] * solarSize * solarData[worstMonth]));

	cout << result << endl;

	cout << "To reach a minimum coverage of " << percentage*100 << " %, aproximately " << result << " PVEs will be needed." << endl;

	calculatePercentage(solarData, consumptionData, solarSize, result, consumption);
}



std::vector<float> parseData(string file) {
	std::ifstream infile(file);

	std::string line;	
	list<std::vector<float>> data;
	std::vector<float> monthlyProduction;
	std::vector<float> monthlyShare;
	std::vector<float> coefficients;
	coefficients.clear();
	
	data.clear();

	
	while (std::getline(infile, line))
	{
		
		int commas = 0;
		std::stringstream ss(line);
	    for (float i; ss >> i;) {
	    	commas ++;
	        monthlyProduction.push_back(i);    
	        if (ss.peek() == ',')
	            ss.ignore();
	    }

	    float power;
	    float total = 0;
	    int flag;
	   
	   	for (int i = 0; i < 12; i++)
	   	{
	   		total += monthlyProduction[i];
	   	}
	   	
	    
	    for (int i = 0; i < 12; i++)
	   	{
	    	monthlyShare.push_back(monthlyProduction[i]/total);
	  	}


	    if (monthlyProduction.size() == 13)
	    {
	    	power = monthlyProduction[12];
	    	monthlyShare.push_back(total/power);
	    }
	    

	    data.push_back(monthlyShare);

		
	    monthlyProduction.clear();
		monthlyShare.clear();	    
	}


	list<std::vector<float>>::iterator dataIterator;
	


	
	for (int i = 0; i < (*data.begin()).size(); i++)
	{
		float adder = 0;
		int counter = 0;
		for (dataIterator = data.begin(); dataIterator != data.end(); dataIterator++)
		{
			adder += (*dataIterator)[i];
			counter ++;
		}
		adder /= counter;
		coefficients.push_back(adder);
	}
	
	return coefficients;
}


int main(int argc, char **argv)
{
	string solarData, consumptionData;
	float count = 0;
	float size = 0;
	float percentage = 0;
	float consumption = 0;
	

	int opt = 0;
    static struct option long_options[] = {
    	{"count",		required_argument,	0,	'a'},
    	{"size",		required_argument,	0,	'b'},
    	{"consumption",	required_argument,	0,	'c'},
    	{"solardata",	required_argument,	0,	'd'},
    	{"condata",		required_argument,	0,	'e'},
    	{"percentage",	required_argument,	0,	'f'},
    	{0,				0,					0,		0} 	
    };

    int long_index = 0;
    while ((opt = getopt_long_only(argc, argv, "", long_options, &long_index)) != -1)
        switch (opt)
    {
        case 'a':
        count = atof(optarg);
        break;
        case 'b':
        size = atof(optarg);
        break;
        case 'c':
        consumption = atof(optarg);
        break;
        case 'd':
        solarData = optarg;
        break;
        case 'e':
        consumptionData = optarg;
        break;
        case 'f':
        percentage = atof(optarg);
        break;
      default:
      exit(-1);
  }

  	std::vector<float> solar = parseData(solarData);
	std::vector<float> consumptionAvg = parseData(consumptionData);
	

	if (percentage == 0 && count > 0 && size > 0)
	{
		calculatePercentage(solar, consumptionAvg, size, count, consumption);
	}
	else if (percentage > 0 && count == 0 && size > 0)
	{
		calculateCount(solar, consumptionAvg, size, percentage/100, consumption);
	}
	else if (percentage > 0 && count > 0 && size == 0)
	{
		calculateOutput(solar, consumptionAvg, count, percentage/100, consumption);
	}
	
	
	return 0;
}