//======== Copyright (c) 2017, FIT VUT Brno, All rights reserved. ============//
//
// Purpose:     Test Driven Development - priority queue code
//
// $NoKeywords: $ivs_project_1 $tdd_code.cpp
// $Author:     JMENO PRIJMENI <xlogin00@stud.fit.vutbr.cz>
// $Date:       $2017-01-04
//============================================================================//
/**
 * @file tdd_code.cpp
 * @author JMENO PRIJMENI
 * 
 * @brief Implementace metod tridy prioritni fronty.
 */

#include <stdlib.h>
#include <stdio.h>

#include "tdd_code.h"

//============================================================================//
// ** ZDE DOPLNTE IMPLEMENTACI **
//
// Zde doplnte implementaci verejneho rozhrani prioritni fronty (Priority Queue)
// 1. Verejne rozhrani fronty specifikovane v: tdd_code.h (sekce "public:")
//    - Konstruktor (PriorityQueue()), Destruktor (~PriorityQueue())
//    - Metody Insert/Remove/Find a GetHead
//    - Pripadne vase metody definovane v tdd_code.h (sekce "protected:")
//
// Cilem je dosahnout plne funkcni implementace prioritni fronty implementovane
// pomoci tzv. "double-linked list", ktera bude splnovat dodane testy 
// (tdd_tests.cpp).
//============================================================================//

PriorityQueue::PriorityQueue()
{
	root = NULL;
}

PriorityQueue::~PriorityQueue()
{
	Element_t *qElem = GetHead();

	while (qElem != NULL) {
		Element_t *current = qElem;
		qElem = qElem->pNext;
		delete current;
	}
}

void PriorityQueue::Insert(int value)
{
	//Find the first element if it exists
	Element_t *element = GetHead();
	//Create a new queue element
	Element_t *newElement = new Element_t();

	//Initialise the new queue element
	newElement->value = value;
	newElement->pPrev = NULL;
	newElement->pNext = NULL;

	int counter = 0;

	//If there are no elements in the queue
	//We set the new element as root
	if (element == NULL) {
		root = newElement;
		return;
	}

	else {			
		while (element != NULL) {

			//If there is only one element in the queue

			if (element->pPrev == NULL && element->pNext == NULL) {
				if (element->value > newElement->value) {
					newElement->pNext = element;
					root = newElement;
					element->pPrev = root;
					
					return;
				}
				else {
					newElement->pPrev = element;
					element->pNext = newElement;
					
					return;
				}
			}

			//If there are more elements in the queue

			//The current element is the first in the queue
			else if (element->pPrev == NULL && element->pNext != NULL){
				if (element->value > newElement->value) {
					newElement->pNext = element;
					root = newElement;
					element->pPrev = root;
					
					return;
				}
			}

			//The current element is the last in the queue
			else if (element->pPrev != NULL && element->pNext == NULL) {
				if (element->value > newElement->value) {
					newElement->pPrev = element->pPrev;
					newElement->pNext = element;
					element->pPrev->pNext = newElement;
					element->pPrev = element->pPrev->pNext;
					
					return;
				}
				else {
					newElement->pPrev = element;
					element->pNext = newElement;
					
					return;
				}
			}

			else if (element->pPrev->value <= newElement->value && element->value >= newElement->value) {
				newElement->pPrev = element->pPrev;
				newElement->pNext = element;
				element->pPrev->pNext = newElement;
				element->pPrev = newElement;
				
				return;	

			}
			Element_t *help = GetHead();
			while (help != NULL){
				
				help = help->pNext;
			}
			

			element = element->pNext;
		}
	}
}

bool PriorityQueue::Remove(int value)
{
	//Perform a search for the element in queue
	Element_t *element = Find(value);

	//If the element exists, continue 
	if (element != NULL){

		//If there is only one element in the queue
		if (element->pPrev == NULL && element->pNext == NULL) {
			root = NULL;
			delete element;
			return true;
		}

		//If the element is the first in the queue
		else if (element->pPrev == NULL && element->pNext != NULL) {
			element->pNext->pPrev = NULL;
			root = element->pNext;
			delete element;
			return true;
		}

		//If the element is the last in the queue
		else if (element->pPrev != NULL && element->pNext == NULL) {
			element->pPrev->pNext = NULL;
			delete element;
			return true;
		}

		//If the element is in between other elements
		else if (element->pPrev != NULL && element->pNext != NULL) {
			element->pPrev->pNext = element->pNext;
			element->pNext->pPrev = element->pPrev;
			delete element;
			return true;
		}
	}
	else {
		return false;
	}

	//If the desired element doesnt exist, return false
    
}

PriorityQueue::Element_t *PriorityQueue::Find(int value)
{
	//Find the first element
	Element_t *element = GetHead();

	//Iterate through all the elements while the 
	//value we are looking for is smaller than the current
	//element and check if the values are matching
	while (element != NULL){
		//if (value <= element->value){
			if (element->value == value){
				return element;
			}
			element = element->pNext;
		//}
	}
    return NULL;
}

PriorityQueue::Element_t *PriorityQueue::GetHead()
{
	//Returns the root element
    return root;
}

/*** Konec souboru tdd_code.cpp ***/
