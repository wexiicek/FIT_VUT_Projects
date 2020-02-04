
/* c201.c *********************************************************************}
{* Téma: Jednosměrný lineární seznam
**
**                     Návrh a referenční implementace: Petr Přikryl, říjen 1994
**                                          Úpravy: Andrea Němcová listopad 1996
**                                                   Petr Přikryl, listopad 1997
**                                Přepracované zadání: Petr Přikryl, březen 1998
**                                  Přepis do jazyka C: Martin Tuček, říjen 2004
**                                            Úpravy: Kamil Jeřábek, září 2018
**
** Implementujte abstraktní datový typ jednosměrný lineární seznam.
** Užitečným obsahem prvku seznamu je celé číslo typu int.
** Seznam bude jako datová abstrakce reprezentován proměnnou typu tList.
** Definici konstant a typů naleznete v hlavičkovém souboru c201.h.
**
** Vaším úkolem je implementovat následující operace, které spolu s výše
** uvedenou datovou částí abstrakce tvoří abstraktní datový typ tList:
**
**      InitList ...... inicializace seznamu před prvním použitím,
**      DisposeList ... zrušení všech prvků seznamu,
**      InsertFirst ... vložení prvku na začátek seznamu,
**      First ......... nastavení aktivity na první prvek,
**      CopyFirst ..... vrací hodnotu prvního prvku,
**      DeleteFirst ... zruší první prvek seznamu,
**      PostDelete .... ruší prvek za aktivním prvkem,
**      PostInsert .... vloží nový prvek za aktivní prvek seznamu,
**      Copy .......... vrací hodnotu aktivního prvku,
**      Actualize ..... přepíše obsah aktivního prvku novou hodnotou,
**      Succ .......... posune aktivitu na další prvek seznamu,
**      Active ........ zjišťuje aktivitu seznamu.
**
** Při implementaci funkcí nevolejte žádnou z funkcí implementovaných v rámci
** tohoto příkladu, není-li u dané funkce explicitně uvedeno něco jiného.
**
** Nemusíte ošetřovat situaci, kdy místo legálního ukazatele na seznam předá
** někdo jako parametr hodnotu NULL.
**
** Svou implementaci vhodně komentujte!
**
** Terminologická poznámka: Jazyk C nepoužívá pojem procedura.
** Proto zde používáme pojem funkce i pro operace, které by byly
** v algoritmickém jazyce Pascalovského typu implemenovány jako
** procedury (v jazyce C procedurám odpovídají funkce vracející typ void).
**/

#include "c201.h"

int errflg;
int solved;

void Error() {
/*
** Vytiskne upozornění na to, že došlo k chybě.
** Tato funkce bude volána z některých dále implementovaných operací.
**/
    printf ("*ERROR* The program has performed an illegal operation.\n");
    errflg = TRUE;                      /* globální proměnná -- příznak chyby */
}

void InitList (tList *L) {
/*
** Provede inicializaci seznamu L před jeho prvním použitím (tzn. žádná
** z následujících funkcí nebude volána nad neinicializovaným seznamem).
** Tato inicializace se nikdy nebude provádět nad již inicializovaným
** seznamem, a proto tuto možnost neošetřujte. Vždy předpokládejte,
** že neinicializované proměnné mají nedefinovanou hodnotu.
**/	

	L -> First = NULL;
	L -> Act = NULL;
}

void DisposeList (tList *L) {
/*
** Zruší všechny prvky seznamu L a uvede seznam L do stavu, v jakém se nacházel
** po inicializaci. Veškerá paměť používaná prvky seznamu L bude korektně
** uvolněna voláním operace free.
***/
	
	while (L -> First != NULL){
	//We iterate through the list, while there are still elements left

		tElemPtr helper = L -> First;
		//Set a new helping element to the value of First in list

		if (L -> First == L -> Act)
			L -> Act = NULL;
			//If the element is active and first, it is the last remaining element.
			//Therefore by deleting it, we lose the activity in the list.

		L -> First = L -> First -> ptr;
		//Set the first element to the value of next element
		free(helper);
		//Remove the element by freeing its' memory
	}

	L -> First = NULL;
	L -> Act = NULL;
	//Then we set First and Active elements as NULL,
	//effectively resetting the list to the former state
}

void InsertFirst (tList *L, int val) {
/*
** Vloží prvek s hodnotou val na začátek seznamu L.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci Error().
**/
    
	tElemPtr helper = malloc(sizeof(struct tElem)); 
	//Define a new helping element

	if(helper != NULL) {
		helper -> data = val;
		//Assign the val to a new element
		helper -> ptr = L -> First;
		//Set the First element as second
		L -> First = helper; 
		//Set the new element as first
		return;	 
	}
 
	Error();
}

void First (tList *L) {
/*
** Nastaví aktivitu seznamu L na jeho první prvek.
** Funkci implementujte jako jediný příkaz, aniž byste testovali,
** zda je seznam L prázdný.
**/
	
	L -> Act = L -> First;    
	//Set the activity to the first element
}

void CopyFirst (tList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu prvního prvku seznamu L.
** Pokud je seznam L prázdný, volá funkci Error().
**/
	
	if ( L -> First != NULL) {
		*val = L -> First -> data;
		//If the list is not empty (there is a first element), we set val to the value of first element
		return;
	} 
	Error();
}

void DeleteFirst (tList *L) {
/*
** Zruší první prvek seznamu L a uvolní jím používanou paměť.
** Pokud byl rušený prvek aktivní, aktivita seznamu se ztrácí.
** Pokud byl seznam L prázdný, nic se neděje.
**/
	
	if(L -> First != NULL){
		if (L -> First == L -> Act)
			L -> Act = NULL;
			//List is not active if we remove an active element
		
	 	tElemPtr helper = L -> First; 
		 //Saving first element into helper var
	 	L -> First = L -> First -> ptr; 
		 //Moving next element to the first position
 		free(helper);
		 //Freeing allocated memory
	}
}	

void PostDelete (tList *L) {
/* 
** Zruší prvek seznamu L za aktivním prvkem a uvolní jím používanou paměť.
** Pokud není seznam L aktivní nebo pokud je aktivní poslední prvek seznamu L,
** nic se neděje.
**/
	
	if((L -> Act != NULL) && (L -> Act -> ptr != NULL)) { 
		if((L -> Act -> ptr -> ptr) != NULL){
			//If the element is not second to last, we move the second next element and then remove

			tElemPtr helper = L -> Act -> ptr -> ptr;
			//Grab the second next element
			free(L -> Act -> ptr);
			//Remove the desired element
			L -> Act -> ptr = helper;
			//Move the second next element to the place of the removed one.
			return;
		}
		free(L -> Act -> ptr);
		//If it's second to last, we remove the last element
		L -> Act -> ptr = NULL;
		//Then we set next element as NULL, because there are no more elements to be moved
	}
}

void PostInsert (tList *L, int val) {
/*
** Vloží prvek s hodnotou val za aktivní prvek seznamu L.
** Pokud nebyl seznam L aktivní, nic se neděje!
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** zavolá funkci Error().
**/
	
	if (L -> Act != NULL) { 

		tElemPtr helper = malloc(sizeof(struct tElem)); 

		if(helper != NULL) {
			helper -> data = val; 
			//Assign the value to a new elements' data
    		helper -> ptr = L -> Act -> ptr; 
			//The pointer of the new element is next from the Active
    		L -> Act -> ptr = helper; 
			//Replace the Active element with the new one
			return;
		}	
		Error();    	
	} 
}

void Copy (tList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu aktivního prvku seznamu L.
** Pokud seznam není aktivní, zavolá funkci Error().
**/
	
	if (L -> Act != NULL) {
		*val = L -> Act -> data;
		//If we have an active element, we set the pointer val to the value of active elements data
	 	return;
 	} 
 	Error();
}

void Actualize (tList *L, int val) {
/*
** Přepíše data aktivního prvku seznamu L hodnotou val.
** Pokud seznam L není aktivní, nedělá nic!
**/
	
	if (L -> Act != NULL)
	 	L -> Act -> data = val;
		//If we have an active element, we change data to val
}

void Succ (tList *L) {
/*
** Posune aktivitu na následující prvek seznamu L.
** Všimněte si, že touto operací se může aktivní seznam stát neaktivním.
** Pokud není předaný seznam L aktivní, nedělá funkce nic.
**/
	
	if (L -> Act != NULL){
		if(L -> Act -> ptr != NULL){
			L -> Act = L -> Act -> ptr;
			//If the list is active and there is a next element, we set activity to it
			return;
		}
		L -> Act = NULL;
		//If the list is active and there is NOT any next element, we lose activity
	}
}

int Active (tList *L) {
/*
** Je-li seznam L aktivní, vrací nenulovou hodnotu, jinak vrací 0.
** Tuto funkci je vhodné implementovat jedním příkazem return. 
**/
	
	return ((L -> Act != NULL) ? 1 : 0);
	//If there is an active element (it's not NULL), we return 1
}

/* Konec c201.c */
