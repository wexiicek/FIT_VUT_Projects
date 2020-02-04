
/* c206.c **********************************************************}
{* Téma: Dvousměrně vázaný lineární seznam
**
**                   Návrh a referenční implementace: Bohuslav Křena, říjen 2001
**                            Přepracované do jazyka C: Martin Tuček, říjen 2004
**                                            Úpravy: Kamil Jeřábek, září 2018
**
** Implementujte abstraktní datový typ dvousměrně vázaný lineární seznam.
** Užitečným obsahem prvku seznamu je hodnota typu int.
** Seznam bude jako datová abstrakce reprezentován proměnnou
** typu tDLList (DL znamená Double-Linked a slouží pro odlišení
** jmen konstant, typů a funkcí od jmen u jednosměrně vázaného lineárního
** seznamu). Definici konstant a typů naleznete v hlavičkovém souboru c206.h.
**
** Vaším úkolem je implementovat následující operace, které spolu
** s výše uvedenou datovou částí abstrakce tvoří abstraktní datový typ
** obousměrně vázaný lineární seznam:
**
**      DLInitList ...... inicializace seznamu před prvním použitím,
**      DLDisposeList ... zrušení všech prvků seznamu,
**      DLInsertFirst ... vložení prvku na začátek seznamu,
**      DLInsertLast .... vložení prvku na konec seznamu,
**      DLFirst ......... nastavení aktivity na první prvek,
**      DLLast .......... nastavení aktivity na poslední prvek,
**      DLCopyFirst ..... vrací hodnotu prvního prvku,
**      DLCopyLast ...... vrací hodnotu posledního prvku,
**      DLDeleteFirst ... zruší první prvek seznamu,
**      DLDeleteLast .... zruší poslední prvek seznamu,
**      DLPostDelete .... ruší prvek za aktivním prvkem,
**      DLPreDelete ..... ruší prvek před aktivním prvkem,
**      DLPostInsert .... vloží nový prvek za aktivní prvek seznamu,
**      DLPreInsert ..... vloží nový prvek před aktivní prvek seznamu,
**      DLCopy .......... vrací hodnotu aktivního prvku,
**      DLActualize ..... přepíše obsah aktivního prvku novou hodnotou,
**      DLSucc .......... posune aktivitu na další prvek seznamu,
**      DLPred .......... posune aktivitu na předchozí prvek seznamu,
**      DLActive ........ zjišťuje aktivitu seznamu.
**
** Při implementaci jednotlivých funkcí nevolejte žádnou z funkcí
** implementovaných v rámci tohoto příkladu, není-li u funkce
** explicitně uvedeno něco jiného.
**
** Nemusíte ošetřovat situaci, kdy místo legálního ukazatele na seznam 
** předá někdo jako parametr hodnotu NULL.
**
** Svou implementaci vhodně komentujte!
**
** Terminologická poznámka: Jazyk C nepoužívá pojem procedura.
** Proto zde používáme pojem funkce i pro operace, které by byly
** v algoritmickém jazyce Pascalovského typu implemenovány jako
** procedury (v jazyce C procedurám odpovídají funkce vracející typ void).
**/

#include "c206.h"

int errflg;
int solved;

void DLError() {
/*
** Vytiskne upozornění na to, že došlo k chybě.
** Tato funkce bude volána z některých dále implementovaných operací.
**/	
    printf ("*ERROR* The program has performed an illegal operation.\n");
    errflg = TRUE;             /* globální proměnná -- příznak ošetření chyby */
    return;
}

void DLInitList (tDLList *L) {
/*
** Provede inicializaci seznamu L před jeho prvním použitím (tzn. žádná
** z následujících funkcí nebude volána nad neinicializovaným seznamem).
** Tato inicializace se nikdy nebude provádět nad již inicializovaným
** seznamem, a proto tuto možnost neošetřujte. Vždy předpokládejte,
** že neinicializované proměnné mají nedefinovanou hodnotu.
**/
    
    L -> First = NULL;
    L -> Act = NULL;
    L -> Last = NULL;
    //Set all indexes to NULL value
}

void DLDisposeList (tDLList *L) {
/*
** Zruší všechny prvky seznamu L a uvede seznam do stavu, v jakém
** se nacházel po inicializaci. Rušené prvky seznamu budou korektně
** uvolněny voláním operace free. 
**/
	
    while(L -> First != NULL){
        tDLElemPtr helper = L -> First;
        //Create a new helper element

        if (L -> First == L -> Act)
            L -> Act = NULL;
            //If the first is also the active one, it is the last to be removed
            //and we lose the activity

        if (L -> First == L -> Last)
            L -> Last = NULL;
            //If the first is also the last,

        L -> First = L -> First -> rptr; 
        //Move element from the right to the position of the first
        free (helper);
        //Free the memory of element
        }

    L -> First = NULL;
    L -> Act = NULL;
    L -> Last = NULL;
    //Set all the pointers to NULL, effectively resetting 
    //the state to be the same as after initialization
}

void DLInsertFirst (tDLList *L, int val) {
/*
** Vloží nový prvek na začátek seznamu L.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/
	
	tDLElemPtr helper = malloc(sizeof(struct tDLElem));
    //Create a new helper element
    if (helper != NULL){
        helper -> data = val;
        //Set the val as data of helper element
        helper -> rptr = L -> First;
        helper -> lptr = NULL;

        if(L -> Last == NULL)
            L -> Last = helper;
            //If there are no elements in the list, we set our helper element as Last

        else
            L -> First -> lptr = helper;
            //If there are any elements, we set our helper element as Left from First
        
        L -> First = helper; 
        //Then we move the element to the first position
            
        return;
    }
    DLError();
}

void DLInsertLast(tDLList *L, int val) {
/*
** Vloží nový prvek na konec seznamu L (symetrická operace k DLInsertFirst).
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/ 	
	
	tDLElemPtr helper = malloc(sizeof(struct tDLElem));
    //Create a new helper element
    if (helper != NULL){
        helper -> data = val;
        helper -> lptr = L -> Last;
        helper -> rptr = NULL;
        //Same as the task above (DLInsertFirst)

        if (L -> Last == NULL)
            L -> First = helper;

        else
            L -> Last -> rptr = helper;
        
        L -> Last = helper;
        //Same as the task above (DLInsertFirst)
        //but the other way around
        
        return;
    }
    
    DLError();
}

void DLFirst (tDLList *L) {
/*
** Nastaví aktivitu na první prvek seznamu L.
** Funkci implementujte jako jediný příkaz (nepočítáme-li return),
** aniž byste testovali, zda je seznam L prázdný.
**/
	
	L -> Act = L -> First;
    //Sets activity to the first Element
}

void DLLast (tDLList *L) {
/*
** Nastaví aktivitu na poslední prvek seznamu L.
** Funkci implementujte jako jediný příkaz (nepočítáme-li return),
** aniž byste testovali, zda je seznam L prázdný.
**/
	
	L -> Act = L -> Last;
    //Sets activity to the last element
}

void DLCopyFirst (tDLList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu prvního prvku seznamu L.
** Pokud je seznam L prázdný, volá funkci DLError().
**/

    if(L -> First != NULL){
        *val = L -> First -> data;
        //If the list is not empty, we return data of the first Element to val
        return;
    }
    
    DLError();
}

void DLCopyLast (tDLList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu posledního prvku seznamu L.
** Pokud je seznam L prázdný, volá funkci DLError().
**/
	
    if(L -> First != NULL){
       *val = L -> Last -> data;
       //Same as DLCopyFirst but with last element
        return;
    }
    
    DLError();
}

void DLDeleteFirst (tDLList *L) {
/*
** Zruší první prvek seznamu L. Pokud byl první prvek aktivní, aktivita 
** se ztrácí. Pokud byl seznam L prázdný, nic se neděje.
**/
	
	if(L -> First != NULL){

        if(L -> Act == L -> First)
            L -> Act = NULL;
             //If the first element exists and it's active, we lose the activity

        tDLElemPtr helper = L -> First;
        //Move the first element to a helper element
        L -> First = L -> First -> rptr;
        //Move the second element to fill the first position
        L -> First -> lptr = NULL;
        //Change the new first elements' left pointer to NULL
        free(helper); 
        //Remove the original first element by freeing the memory
    }  
}	

void DLDeleteLast (tDLList *L) {
/*
** Zruší poslední prvek seznamu L. Pokud byl poslední prvek aktivní,
** aktivita seznamu se ztrácí. Pokud byl seznam L prázdný, nic se neděje.
**/ 
	
	if(L -> First != NULL){
        
        if(L -> Act == L -> Last)
            L -> Act = NULL;
            //If the last element is also active, we lose the activity

        tDLElemPtr helper = L -> Last;
        //Move the last element to a helper element
        L -> Last = L -> Last -> lptr;
        //Set the second to last element as last
        free(helper);
        //Remove the original last element by freeing the memory

        if (L -> Last == NULL)
            L -> First = NULL;
            //If we removed the last element, the list becomes empty (there is no first element)

        else
            L -> Last -> rptr = NULL;
            //Else we remove the right pointer of the last element
	}
}

void DLPostDelete (tDLList *L) {
/*
** Zruší prvek seznamu L za aktivním prvkem.
** Pokud je seznam L neaktivní nebo pokud je aktivní prvek
** posledním prvkem seznamu, nic se neděje.
**/
	
	if((L -> First != NULL) && (L -> Act != NULL) && (L -> Act != L -> Last)){
        tDLElemPtr helper = L -> Act -> rptr;
        //If the list is not empty, it is active and the element to be removed is not last,
        //we create a helper element
		L -> Act -> rptr = L -> Act -> rptr -> rptr;
        //First we move past the element to be removed
		
        if(L -> Last == helper) { //if the element is last
			L -> Last = L -> Act;
			L -> Act -> rptr = NULL;
		}
		
        else 
			helper -> rptr -> lptr = L -> Act; //Skip the element
        free(helper);
        }
}

void DLPreDelete (tDLList *L) {
/*
** Zruší prvek před aktivním prvkem seznamu L .
** Pokud je seznam L neaktivní nebo pokud je aktivní prvek
** prvním prvkem seznamu, nic se neděje.
**/
	
	if((L -> First != NULL) && (L -> Act != NULL) && (L -> Act != L -> First)){
		tDLElemPtr helper = L -> Act -> lptr;
		L -> Act -> lptr = L -> Act -> lptr -> lptr; 
		//Skip the element

		if(L -> First == helper) { 
		//If the element is last
			L -> First = L -> Act;
			L -> Act -> lptr = NULL;
		}

		else 
			helper -> lptr -> rptr = L -> Act; 
			//Skip the element

        free(helper);
    }
}

void DLPostInsert (tDLList *L, int val) {
/*
** Vloží prvek za aktivní prvek seznamu L.
** Pokud nebyl seznam L aktivní, nic se neděje.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/
	
	if(!(L -> Act == NULL)){
        tDLElemPtr helper = (tDLElemPtr) malloc(sizeof(struct tDLElem));
        if(!(helper == NULL)){
            helper -> data = val;
            helper -> lptr = L -> Act;
            helper -> rptr = L -> Act -> rptr;
            //Set helper values to the values of the previous element
            
            //If the element is last
            if(L -> Last == L->Act)
                L -> Last = helper;

            //If the element is not last
            else
                L -> Act -> rptr -> lptr = helper;

            L->Act->rptr = helper; 
            //Set the new element as previous
            return;
	   }
        DLError();
    }

}

void DLPreInsert (tDLList *L, int val) {
/*
** Vloží prvek před aktivní prvek seznamu L.
** Pokud nebyl seznam L aktivní, nic se neděje.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/
	
    if(!(L -> Act == NULL)){
        tDLElemPtr helper = (tDLElemPtr) malloc(sizeof(struct tDLElem));
        //Allocating memory for new helper element
        if(!(helper == NULL)){
            helper -> data = val;
            helper -> lptr = L -> Act -> lptr;
            helper -> rptr = L -> Act;
            //Set helper values to the values of the succesive element

            //If the element is first
            if(L -> First == L->Act)
                L -> First = helper;

            //If the element is not first
            else
                L -> Act -> lptr -> rptr = helper;
            
            L->Act->lptr = helper; 
            //Set the new element as previous
            return;
       }
        DLError();
    }

}

void DLCopy (tDLList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu aktivního prvku seznamu L.
** Pokud seznam L není aktivní, volá funkci DLError ().
**/
		
	if(L -> Act != NULL){
        *val = L -> Act -> data;
        //If there is an active element, we return its' value to val
        return;
	}
	DLError();
}

void DLActualize (tDLList *L, int val) {
/*
** Přepíše obsah aktivního prvku seznamu L.
** Pokud seznam L není aktivní, nedělá nic.
**/
	
    if(L -> Act != NULL)
        L -> Act -> data = val; 
        //If there is an active element, we set its' data to value of val
}

void DLSucc (tDLList *L) {
/*
** Posune aktivitu na následující prvek seznamu L.
** Není-li seznam aktivní, nedělá nic.
** Všimněte si, že při aktivitě na posledním prvku se seznam stane neaktivním.
**/
	
    if (L -> Act != NULL)
        ((L -> Act == L -> Last) ? (L -> Act = NULL) : (L -> Act = L -> Act -> rptr));  
        //If there is an active element,
        // a) and it is last in list, we lose activity
        // b) and it is not last, we set the activity to succesor
}


void DLPred (tDLList *L) {
/*
** Posune aktivitu na předchozí prvek seznamu L.
** Není-li seznam aktivní, nedělá nic.
** Všimněte si, že při aktivitě na prvním prvku se seznam stane neaktivním.
**/
	
    if (L -> Act != NULL)
        ((L -> Act == L -> First) ? (L -> Act = NULL) : (L -> Act = L -> Act -> lptr));
        //If there is an active element,
        // 1) and it is first in list, we lose activity
        // 2) and it is not first, we set the activity to previous
}

int DLActive (tDLList *L) {
/*
** Je-li seznam L aktivní, vrací nenulovou hodnotu, jinak vrací 0.
** Funkci je vhodné implementovat jedním příkazem return.
**/
	
	return ((L -> Act != NULL) ? 1 : 0);
    //Return 1 if there is an active element
}

/* Konec c206.c*/
