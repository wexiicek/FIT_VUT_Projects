/**
* Kostra programu pro 3. projekt IZP 2017/18
*
* @author: Dominik Juriga (xjurig00)
*
* Jednoducha shlukova analyza
* Unweighted pair-group average
* https://is.muni.cz/th/172767/fi_b/5739129/web/web/usrov.html
*/
#include <stdio.h>
#include <stdlib.h>
#include <assert.h>
#include <math.h> // sqrtf
#include <limits.h> // INT_MAX
#include <string.h> //strcpy, strcmp

/*****************************************************************
* Ladici makra. Vypnout jejich efekt lze definici makra
* NDEBUG, napr.:
*   a) pri prekladu argumentem prekladaci -DNDEBUG
*   b) v souboru (na radek pred #include <assert.h>
*      #define NDEBUG
*/
#ifdef NDEBUG
#define debug(s)
#define dfmt(s, ...)
#define dint(i)
#define dfloat(f)
#else

// vypise ladici retezec
#define debug(s) printf("- %s\n", s)

// vypise formatovany ladici vystup - pouziti podobne jako printf
#define dfmt(s, ...) printf(" - "__FILE__":%u: "s"\n",__LINE__,__VA_ARGS__)

// vypise ladici informaci o promenne - pouziti dint(identifikator_promenne)
#define dint(i) printf(" - " __FILE__ ":%u: " #i " = %d\n", __LINE__, i)

// vypise ladici informaci o promenne typu float - pouziti
// dfloat(identifikator_promenne)
#define dfloat(f) printf(" - " __FILE__ ":%u: " #f " = %g\n", __LINE__, f)

#endif

/*****************************************************************
* Deklarace potrebnych datovych typu:
*
* TYTO DEKLARACE NEMENTE
*
*   struct obj_t - struktura objektu: identifikator a souradnice
*   struct cluster_t - shluk objektu:
*      pocet objektu ve shluku,
*      kapacita shluku (pocet objektu, pro ktere je rezervovano
*          misto v poli),
*      ukazatel na pole shluku.
*/
int premium_case;

struct obj_t {
    int id;
    float x;
    float y;
};

struct cluster_t {
    int size;
    int capacity;
    struct obj_t *obj;
};

/*****************************************************************
* Deklarace potrebnych funkci.
*
* PROTOTYPY FUNKCI NEMENTE
*
* IMPLEMENTUJTE POUZE FUNKCE NA MISTECH OZNACENYCH 'TODO'
*
*/

/*
Inicializace shluku 'c'. Alokuje pamet pro cap objektu (kapacitu).
Ukazatel NULL u pole objektu znamena kapacitu 0.
*/
void init_cluster(struct cluster_t *c, int cap) {
    assert(c != NULL);
    assert(cap >= 0);

// TODO
    c->obj = malloc(sizeof(struct obj_t) * cap); //memory allocation for objects
    if(c->obj == NULL) {    //mem allocation check
        c->capacity = 0;
        fprintf(stderr,"Error while allocating memory.\n");
    }
    c->capacity = cap;
    c->size = 0;
}

/*
Odstraneni vsech objektu shluku a inicializace na prazdny shluk.
*/
void clear_cluster(struct cluster_t *c) {
// TODO
    free(c->obj);
    c->size=0;
    c->capacity=0;
    c->obj = NULL;
}

/// Chunk of cluster objects. Value recommended for reallocation.
const int CLUSTER_CHUNK = 10;

/*
Zmena kapacity shluku 'c' na kapacitu 'new_cap'.
*/
struct cluster_t *resize_cluster(struct cluster_t *c, int new_cap) {
// TUTO FUNKCI NEMENTE
    assert(c);
    assert(c->capacity >= 0);
    assert(new_cap >= 0);

    if (c->capacity >= new_cap)
        return c;

    size_t size = sizeof(struct obj_t) * new_cap;

    void *arr = realloc(c->obj, size);
    if (arr == NULL)
        return NULL;

    c->obj = (struct obj_t*)arr;
    c->capacity = new_cap;
    return c;
}

/*
Prida objekt 'obj' na konec shluku 'c'. Rozsiri shluk, pokud se do nej objekt
nevejde.
*/
void append_cluster(struct cluster_t *c, struct obj_t obj) {
// TODO

    if(c->capacity <= c->size)
        resize_cluster(c, c->capacity+CLUSTER_CHUNK); //resize the cluster if there is not enough memory
    c->obj[c->size++]=obj;  //append the cluster, then inc size
}

/*
Seradi objekty ve shluku 'c' vzestupne podle jejich identifikacniho cisla.
*/
void sort_cluster(struct cluster_t *c);

/*
Do shluku 'c1' prida objekty 'c2'. Shluk 'c1' bude v pripade nutnosti rozsiren.
Objekty ve shluku 'c1' budou serazeny vzestupne podle identifikacniho cisla.
Shluk 'c2' bude nezmenen.
*/
void merge_clusters(struct cluster_t *c1, struct cluster_t *c2) {
    assert(c1 != NULL);
    assert(c2 != NULL);

// TODO
    for(int i = 0; i < c2->size; i++) {
        if(c1->size <= c1->capacity) {
            resize_cluster(c1,c1->size+CLUSTER_CHUNK); // resize the cluster if there is more memory needed
        }
        append_cluster(c1,c2->obj[i]);
    }
    sort_cluster(c1); //sort the cluster using quicksort
}

/**********************************************************************/
/* Prace s polem shluku */

/*
Odstrani shluk z pole shluku 'carr'. Pole shluku obsahuje 'narr' polozek
(shluku). Shluk pro odstraneni se nachazi na indexu 'idx'. Funkce vraci novy
pocet shluku v poli.
*/
int remove_cluster(struct cluster_t *carr, int narr, int idx) {
    assert(idx < narr);
    assert(narr > 0);

// TODO

    clear_cluster(&carr[idx]);                     //remove a certain cluster
    for (int i = idx; i < narr - 1; i++) {         //& move all the following
        carr[i] = carr[i + 1];                     //back by one to fill the gap
    }

    return narr - 1;
}

/*
Pocita Euklidovskou vzdalenost mezi dvema objekty.
*/
float obj_distance(struct obj_t *o1, struct obj_t *o2) {
    assert(o1 != NULL);
    assert(o2 != NULL);

// TODO

    float dist1 = pow((o1->x - o2->x),2.0); // distance1 ^2
    float dist2 = pow((o1->y - o2->y),2.0); // distance2 ^2
    float dist = sqrtf(dist1 + dist2);      // calculating the distance (sq root of dist1+dist2)
    return dist;
}

/*
Pocita vzdalenost dvou shluku.
*/
float cluster_distance(struct cluster_t *c1, struct cluster_t *c2) {
    assert(c1 != NULL);
    assert(c1->size > 0);
    assert(c2 != NULL);
    assert(c2->size > 0);

// TODO
    float dist = 0;
    float total = obj_distance(&c1->obj[0], &c2->obj[0]);

    switch (premium_case) {
    case 1: { //nearest
        for(int i = 0; i < c1->size; i++) {
            for(int y = 0; y < c2->size; y++) {
                dist = obj_distance(&c1->obj[i], &c2->obj[y]);
                if(total > dist) {
                    total = dist;
                }
            }
        }
        return total;
        break;
    }

    case 2: { //furthest
        for(int i = 0; i < c1->size; i++) {
            for(int y = 0; y < c2->size; y++) {
                dist = obj_distance(&c1->obj[i], &c2->obj[y]);
                if(total < dist) {
                    total = dist;
                }
            }
        }
        return total;
        break;
    }

    case 3: { //avg
        int cntr = 0;
        for(int i = 0 ; i < c1->size; i++) {
            for(int j = 0; j < c2->size; j++) {
                dist += obj_distance(&c1->obj[i], &c2->obj[j]); //calculates distance between all points
                cntr+=1;
            }
        }
        return dist/cntr;       //returns distance which is equal to all distances of points / count
        break;
    }
    }
    return 0;
}

/*
Funkce najde dva nejblizsi shluky. V poli shluku 'carr' o velikosti 'narr'
hleda dva nejblizsi shluky. Nalezene shluky identifikuje jejich indexy v poli
'carr'. Funkce nalezene shluky (indexy do pole 'carr') uklada do pameti na
adresu 'c1' resp. 'c2'.
*/
void find_neighbours(struct cluster_t *carr, int narr, int *c1, int *c2) {
    assert(narr > 0);

// TODO

    float dist=0;
    float newDist = INT_MAX;
    for(int i = 0; i < narr; i++) {
        for(int j = 1; j < narr; j++) {
            if(i != j){
                dist=cluster_distance(&carr[i],&carr[j]);       //calculate the distance between the points
            }
            if(dist<newDist) {
                *c1=i;
                *c2=j;
                newDist = dist;
            }
        }
    }
}

// pomocna funkce pro razeni shluku
static int obj_sort_compar(const void *a, const void *b) {
// TUTO FUNKCI NEMENTE
    const struct obj_t *o1 = (const struct obj_t *)a;
    const struct obj_t *o2 = (const struct obj_t *)b;
    if (o1->id < o2->id) return -1;
    if (o1->id > o2->id) return 1;
    return 0;
}

/*
Razeni objektu ve shluku vzestupne podle jejich identifikatoru.
*/
void sort_cluster(struct cluster_t *c) {
// TUTO FUNKCI NEMENTE
    qsort(c->obj, c->size, sizeof(struct obj_t), &obj_sort_compar);
}

/*
Tisk shluku 'c' na stdout.
*/
void print_cluster(struct cluster_t *c) {
// TUTO FUNKCI NEMENTE
    for (int i = 0; i < c->size; i++) {
        if (i) putchar(' ');
        printf("%d[%g,%g]", c->obj[i].id, c->obj[i].x, c->obj[i].y);
    }
    putchar('\n');
}

/*
Ze souboru 'filename' nacte objekty. Pro kazdy objekt vytvori shluk a ulozi
jej do pole shluku. Alokuje prostor pro pole vsech shluku a ukazatel na prvni
polozku pole (ukalazatel na prvni shluk v alokovanem poli) ulozi do pameti,
kam se odkazuje parametr 'arr'. Funkce vraci pocet nactenych objektu (shluku).
V pripade nejake chyby uklada do pameti, kam se odkazuje 'arr', hodnotu NULL.
*/
int load_clusters(char *filename, struct cluster_t **arr) {
    assert(arr != NULL);

// TODO
    FILE *file=fopen(filename,"r");
    if( ! file) {
        fprintf(stderr,"%s could not be opened.\n", filename);  //file open check
        return -1;
    }

    char str[100];
    int count = 0;
    int i = 0;
    struct obj_t objekt;

    if(fgets(str, 100, file) != NULL) {             //loading the first line and cluster count
        if(sscanf(str, "count=%d", &count) != 1 || count <= 0) {
            if(fclose(file) == EOF) {
                fprintf(stderr, "%s could not be closed.\n", filename);
            }
            return -1;
        }

        *arr = malloc(sizeof(struct cluster_t) * count);        //allocate the memory for the clusters
        if(! arr) {
            fprintf(stderr,"Error while allocating memory.\n");
            if(fclose(file) == EOF) {
                fprintf(stderr, "%s could not be closed.\n", filename);
            }
            return -1;
        }
    } else {
        fprintf(stderr, "%s contains invalid input.\n",filename );
        return -1;
    }

    for(i = 0; i < count; i++) {
        if(fgets(str, 100, file) != NULL) {
            if( (sscanf(str, "%d %f %f", &objekt.id, &objekt.x, &objekt.y) < 3) ||         //load the objects with id, x and y coord.
                    (objekt.x < 0 || objekt.x > 1000 || objekt.y < 0 || objekt.y > 1000) ) {       //check if the object is valid
                fprintf(stderr, "Object %sis incorrect.\n", str );
                if(fclose(file) == EOF) {
                    fprintf(stderr, "%s could not be closed.\n", filename);
                }
                for(int y = 0; y < i; y++) { //remove already allocated clusters
                    clear_cluster(&(*arr)[y]);
                }
                return -1;
            }
            init_cluster(&(*arr)[i], 1);
            append_cluster(&(*arr)[i], objekt);
        } else {
            if(fclose(file) == EOF) {
                fprintf(stderr, "%s could not be closed.\n", filename);
            }
            return -1;
        }
    }

    if(fclose(file) == EOF) {
        fprintf(stderr, "%s could not be closed.\n", filename);
        return -1;
    }
    return count;
}
/*
Tisk pole shluku. Parametr 'carr' je ukazatel na prvni polozku (shluk).
Tiskne se prvnich 'narr' shluku.
*/
void print_clusters(struct cluster_t *carr, int narr) {
    printf("Clusters:\n");
    for (int i = 0; i < narr; i++) {
        printf("cluster %d: ", i);
        print_cluster(&carr[i]);
    }
}

int main(int argc, char *argv[]) {
    struct cluster_t *clusters;

// TODO

    char filename[101];
    int clusterCount;

    if(argc==4 || argc == 3) {
        premium_case = 3;
        int c1;
        int c2;
        int newClusters;
        if(argc == 4) {

            /*
                Argument check:
                  last argument calls the method
                  second to last argc has to be integer
                else returns invalid input
            */

            if((strcmp(argv[argc-1], "--min")== 0) && (strtol(argv[argc-2], NULL, 10) > 0))
                premium_case = 1;
            else if((strcmp(argv[argc-1], "--max")== 0) && (strtol(argv[argc-2], NULL, 10) > 0))
                premium_case = 2;
            else if((strcmp(argv[argc-1], "--avg")== 0) && (strtol(argv[argc-2], NULL, 10) > 0))
                premium_case = 3;
            else {
                fprintf(stderr,"Invalid input.\n");     //cluster count check, must be greater than 0
                return -1;
            }
            newClusters = strtol(argv[2],NULL,10);

        }
        if (argc == 3) {

            /*
                Checks whether the last argument calls the method
                or sets the amount of clusters
            */

            if(strtol(argv[argc-1],NULL,10) > 0){
                newClusters = strtol(argv[argc-1],NULL,10);
                premium_case = 3;
            }
            else if (strcmp(argv[argc-1], "--min") == 0) {
                newClusters = 1;
                premium_case = 1;
            } else if (strcmp(argv[argc-1], "--max") == 0) {
                newClusters = 1;
                premium_case = 2;
            } else if (strcmp(argv[argc-1], "--avg") == 0) {
                newClusters = 1;
                premium_case = 3;
            }
        }
        strcpy(filename,argv[1]); //initializing filename
        clusterCount=load_clusters(filename,&clusters); //initializing object count
        if (clusterCount == -1) {
            free(clusters);
            return -1;      //if the file cant be opened, end the program
        }

        while(clusterCount != newClusters) {
            find_neighbours(clusters,clusterCount,&c1,&c2);
            merge_clusters(&clusters[c1],&clusters[c2]);
            clusterCount=remove_cluster(clusters,clusterCount,c2);
        }
        print_clusters(clusters,clusterCount);
        for(int i=0; i<clusterCount; i++)
            clear_cluster(&(clusters)[i]);  //clear all the clusters
        free(clusters);                     //and free the memory - leak prevention
    }

    else {
        fprintf(stderr, "Invalid input.\n");
        return -1;
    }

    return 0;
}
