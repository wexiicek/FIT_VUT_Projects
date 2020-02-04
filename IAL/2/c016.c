
/* c016.c: **********************************************************}
{* Téma:  Tabulka s Rozptýlenými Položkami
**                      První implementace: Petr Přikryl, prosinec 1994
**                      Do jazyka C prepsal a upravil: Vaclav Topinka, 2005
**                      Úpravy: Karel Masařík, říjen 2014
**                              Radek Hranický, 2014-2018
**
** Vytvořete abstraktní datový typ
** TRP (Tabulka s Rozptýlenými Položkami = Hash table)
** s explicitně řetězenými synonymy. Tabulka je implementována polem
** lineárních seznamů synonym.
**
** Implementujte následující procedury a funkce.
**
**  HTInit ....... inicializuje tabulku před prvním použitím
**  HTInsert ..... vložení prvku
**  HTSearch ..... zjištění přítomnosti prvku v tabulce
**  HTDelete ..... zrušení prvku
**  HTRead ....... přečtení hodnoty prvku
**  HTClearAll ... zrušení obsahu celé tabulky (inicializace tabulky
**                 poté, co již byla použita)
**
** Definici typů naleznete v souboru c016.h.
**
** Tabulka je reprezentována datovou strukturou typu tHTable,
** která se skládá z ukazatelů na položky, jež obsahují složky
** klíče 'key', obsahu 'data' (pro jednoduchost typu float), a
** ukazatele na další synonymum 'ptrnext'. Při implementaci funkcí
** uvažujte maximální rozměr pole HTSIZE.
**
** U všech procedur využívejte rozptylovou funkci hashCode.  Povšimněte si
** způsobu předávání parametrů a zamyslete se nad tím, zda je možné parametry
** předávat jiným způsobem (hodnotou/odkazem) a v případě, že jsou obě
** možnosti funkčně přípustné, jaké jsou výhody či nevýhody toho či onoho
** způsobu.
**
** V příkladech jsou použity položky, kde klíčem je řetězec, ke kterému
** je přidán obsah - reálné číslo.
*/

#include "c016.h"

int HTSIZE = MAX_HTSIZE;
int solved;

/*          -------
** Rozptylovací funkce - jejím úkolem je zpracovat zadaný klíč a přidělit
** mu index v rozmezí 0..HTSize-1.  V ideálním případě by mělo dojít
** k rovnoměrnému rozptýlení těchto klíčů po celé tabulce.  V rámci
** pokusů se můžete zamyslet nad kvalitou této funkce.  (Funkce nebyla
** volena s ohledem na maximální kvalitu výsledku). }
*/

int hashCode ( tKey key ) {
	int retval = 1;
	int keylen = strlen(key);
	for ( int i=0; i<keylen; i++ )
		retval += key[i];
	return ( retval % HTSIZE );
}

/*
** Inicializace tabulky s explicitně zřetězenými synonymy.  Tato procedura
** se volá pouze před prvním použitím tabulky.
*/

void htInit ( tHTable* ptrht ) {
	for (int i = 0; i < HTSIZE; i++)
		(*ptrht)[i] = NULL;
	//Iterating through all the indexes and initializing them to value NULL
}

/* TRP s explicitně zřetězenými synonymy.
** Vyhledání prvku v TRP ptrht podle zadaného klíče key.  Pokud je
** daný prvek nalezen, vrací se ukazatel na daný prvek. Pokud prvek nalezen není, 
** vrací se hodnota NULL.
**
*/

tHTItem* htSearch ( tHTable* ptrht, tKey key ) {

	tHTItem *current = (*ptrht)[hashCode(key)];
	//Obtain first item on given index

	while (!(current == NULL)){
		if (!(strcmp(current -> key, key ) == 0))
			current = current -> ptrnext;
			//Iterating through every item on given index
			//If the item does not have the same key, we check next item
		else
			return current;
			//If the item has the same key, we return it
		
	}
	return NULL;
	//If we didn't manage to find the matching item
}

/* 
** TRP s explicitně zřetězenými synonymy.
** Tato procedura vkládá do tabulky ptrht položku s klíčem key a s daty
** data.  Protože jde o vyhledávací tabulku, nemůže být prvek se stejným
** klíčem uložen v tabulce více než jedenkrát.  Pokud se vkládá prvek,
** jehož klíč se již v tabulce nachází, aktualizujte jeho datovou část.
**
** Využijte dříve vytvořenou funkci htSearch.  Při vkládání nového
** prvku do seznamu synonym použijte co nejefektivnější způsob,
** tedy proveďte.vložení prvku na začátek seznamu.
**/

void htInsert ( tHTable* ptrht, tKey key, tData data ) {

	tHTItem *helper = htSearch(ptrht, key);

	if(helper == NULL){
	//If the item is not in the table yet
		
		helper = (tHTItem *) malloc(sizeof(tHTItem));
		//We create a helper item

		if (!(helper == NULL)){
		//If the allocation was successful..
			helper -> data = data;
			helper -> key = key;
			//Set helper values to the new values

			int index = hashCode(key);
			//Check if there is an item on the given index
			if (!((*ptrht)[index] == NULL))
				helper -> ptrnext = (*ptrht)[index];
				//If there is an item, we have to point to this item
			else
				helper -> ptrnext = NULL;
				//If there is NOT any item, we don't have to point to the next item

			(*ptrht)[index] = helper;
			//Then we insert the new element to the beginning
		}
		else return;
		//If there was a problem with allocation
	}
	else
		helper -> data = data;
		//If the item is already in the table
}

/*
** TRP s explicitně zřetězenými synonymy.
** Tato funkce zjišťuje hodnotu datové části položky zadané klíčem.
** Pokud je položka nalezena, vrací funkce ukazatel na položku
** Pokud položka nalezena nebyla, vrací se funkční hodnota NULL
**
** Využijte dříve vytvořenou funkci HTSearch.
*/

tData* htRead ( tHTable* ptrht, tKey key ) {

	if(!(ptrht == NULL)){
		tHTItem* helper = htSearch(ptrht, key);
		//Trying to find the item by using htSearch
		if(!(helper == NULL))
			return &(helper -> data);
			//If the item was found, return the data
	}
	return NULL;
	//If the item was not found, return NULL
}

/*
** TRP s explicitně zřetězenými synonymy.
** Tato procedura vyjme položku s klíčem key z tabulky
** ptrht.  Uvolněnou položku korektně zrušte.  Pokud položka s uvedeným
** klíčem neexistuje, dělejte, jako kdyby se nic nestalo (tj. nedělejte
** nic).
**
** V tomto případě NEVYUŽÍVEJTE dříve vytvořenou funkci HTSearch.
*/

void htDelete ( tHTable* ptrht, tKey key ) {

	int index = hashCode(key);
	//We gain table indexx
	tHTItem *item = (*ptrht)[index];
	tHTItem *previous = NULL;

	while (!(item == NULL)){
	//Iterate through every item on the given index until we reach the required one or there are no more items
		if (strcmp(item -> key, key) == 0){
		//This is the one that has matching key
			if (!(previous == NULL))
				previous -> ptrnext = item -> ptrnext;
				//If the item is not the first
			else
				(*ptrht)[index] = item -> ptrnext;
				//If the item is first

			free(item);
			//Free the item from memory
			return;
		}
		previous = item;
		//Save the current item as previous
		item = item -> ptrnext;
		//Continue until we eventually have empty index
	}
}

/* TRP s explicitně zřetězenými synonymy.
** Tato procedura zruší všechny položky tabulky, korektně uvolní prostor,
** který tyto položky zabíraly, a uvede tabulku do počátečního stavu.
*/

void htClearAll ( tHTable* ptrht ) {
	int i = 0;
	do {
	//Iterating through every item in the table
		tHTItem *item = (*ptrht)[i];
		tHTItem *next;

		while (!(item == NULL)){
		//Iterating through every item on the given index
			next = item -> ptrnext;
			//Save the next item
			free(item);
			//Free the current item from memory
			item = next;
			//Continue until we eventually have an empty table
		}
		(*ptrht)[i] = NULL;
		//Then set the given index to NULL
	} while (i++ < HTSIZE - 1);
}
