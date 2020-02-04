#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <string.h>

#define ANGLE_MIN 0
#define ANGLE_MAX 1.4
#define HEIGHT_MIN 0
#define HEIGHT_MAX 100
#define ITERATION_MIN 0
#define ITERATION_MAX 14

void print_help() {
    printf("2. Project User Guide:\nThe program can be launched with following arguments:\n");

    printf("%s%s%s%s%s","\t\"--help\"\n\t\tPrints the user guide.\n",
           "\t\"--tan A N M\"\n\t\tCalculates the value of tangent of angle A (in radians),\n",
           "\t\tN is the starting and M is the ending number of iterations. N <= M < 14\n",
           "\t\"[-c X] -m A [B]\"\n\t\tCalculates the distance of an object.\n\t\tA is angle in radians. 0 < A <= 1.4\n",
           "\t\t[B] is optional angle in radians. 0 < B <= 1.4. If both angles are used, calculates height of object.\n"
           "\t\tIf [-c X] is used, distance is calculated with set height (of X).\n");
}

double taylor_tan(double angle, int n) {
    /*
    *       Function returns the result of tangent, using Taylor's polynome
    *       Two arrays of numbers are utilized;
    *       The formula is fixed and can be found online
    */
    double result = 0.;
    double power = angle;
    // numerator array for tangent calculation:
    long long numerator[13] =   {1, 1,  2,  17,   62,   1382,   21844,    929569,     6404582,     443861162,     18888466084,      113927491862,      58870668456604};
    // denominator array for tangent calculation:
    long long denominator[13] = {1, 3, 15, 315, 2835, 155925, 6081075, 638512875, 10854718875, 1856156927625, 194896477400625, 49308808782358125, 3698160658676859375};
    result += (numerator[0]*(power))/denominator[0];
    for (int i = 1; i < n; i++) {
        result += (numerator[i]*(power *=  angle * angle))/denominator[i];
    }
    return result;
}

double cfrac_tan(double x, unsigned int n) {
    /*
    *   Function returns result of tangent calculated by countinued
    *   fractions.
    *  x is angle entered by user as the second argument
    *  n is amount of iterations
    */
    double a, b, cf = 0.;
    for (int k = n; k > 0; k--) {
        a = 2 * k - 1;
        b = a/x;
        cf = 1 / (b - cf);
    }
    return (cf);
}

int tangent(double angle, int lower, int higher) {
    /*
    *   Angle is entered by user, lower and higher are the iterations
    *   in which the tangent should be calculated.
    *   Function prints result of tan function from math.h, result of continued
    *   fractions and result of tangent by Taylor's polynome
    */

    double M = tan(angle);
    for(int I = lower; I <= higher; I++) {
        double C = cfrac_tan(angle, I);
        double T = taylor_tan(angle, I);
        double TE = M - T, CE = M - C;
        if (TE < 0)
            TE *= -1;
        if (CE < 0)
            CE *= -1;
        printf("%d %e %e %e %e %e\n", I, M, T, TE, C, CE);
    }
    return 0;
}

double measure_distance(double alpha, double machineHeight) {
    /*
    *   Functuon returns the didstance between an object and
    *   machine, using tangent.
    *   alpha is angle set by user
    *   machineHeight is height set by user
    */
    double distance = machineHeight / cfrac_tan(alpha, 10);
    return distance;
}

double measure_height(double alpha, double beta, double machineHeight) {
    /*
    *   Function returns height of an object based on goniometry
    *   alpha & beta are angles entered by user
    *   machineHeight is height of machine entered by user
    */
    double objectHeight = 0.;
    double distance = machineHeight / cfrac_tan(alpha, 10);
    objectHeight = cfrac_tan(beta, 10) * distance + machineHeight;
    return objectHeight;
}

int main(int argc, char * argv[]) {
    (void)argc;
    double machineHeight = 1.5;
    if (strcmp(argv[1], "--help") == 0) {
        print_help();
    } else if (strcmp(argv[1], "--tan") == 0) {
        /*if the user wants to calculate tan, arguments have to be valid,
        meaning they have to  be from a certain interval
        following conditions are deciding, whether arguments are valid,
        thus the program should return desired value
        otherwise, it will ask user to call "--help" to see user guide*/
        //if( (strtod(argv[2],NULL) > ANGLE_MIN) && (strtod(argv[2],NULL) <= ANGLE_MAX)) {
        if (((atoi(argv[3]) > ITERATION_MIN) && (atoi(argv[3]) < ITERATION_MAX) ) && ( (atoi(argv[4]) > ITERATION_MIN) && (atoi(argv[4]) < ITERATION_MAX) )) {
            if ( (atoi(argv[3])) < atoi(argv[4]) ) {
                tangent(strtod(argv[2],NULL),atoi(argv[3]),atoi(argv[4]));
            } else {
                fprintf(stderr, "Iteration count is invalid. See --help for user guide.\n");
                return 3;
            }
        } else {
            fprintf(stderr, "Iteration count is invalid. See --help for user guide.\n");
            return 3;
        }
    }

    else if (strcmp(argv[1], "-m") == 0) {
        if ((strtod(argv[2], NULL) > ANGLE_MIN) && (strtod(argv[2], NULL) <= ANGLE_MAX)) {
            if ((strtod(argv[3], NULL) > ANGLE_MIN) && (strtod(argv[3], NULL) <= ANGLE_MAX)) {
                if ((machineHeight > HEIGHT_MIN) && (machineHeight <= HEIGHT_MAX)) {
                    printf("%.10e\n", measure_distance(strtod(argv[2], NULL),machineHeight));
                    printf("%.10e\n", measure_height(strtod(argv[2], NULL), strtod(argv[3], NULL), machineHeight));
                } else {
                    fprintf(stderr, "Height is not from interval. See --help for user guide.\n");
                    return 2;
                }
            } else {
                fprintf(stderr, "Angle is not from interval. See --help for user guide.\n");
                return 2;
            }
        } else {
            fprintf(stderr, "Angle is not from interval. See --help for user guide.\n");
            return 2;
        }
    } else if ((strcmp(argv[1], "-c") == 0) && ((strcmp(argv[3], "-m") == 0))) {
        machineHeight = strtod(argv[2], NULL);
        if ((machineHeight > HEIGHT_MIN) && (machineHeight <= HEIGHT_MAX)) {
            if ( ( (strtod(argv[4], NULL) > ANGLE_MIN) && (strtod(argv[4], NULL) <= ANGLE_MAX )) && ( (strtod(argv[5], NULL) > ANGLE_MIN) && (strtod(argv[5], NULL) <= ANGLE_MAX ) )) {
                printf("%.10e\n", measure_distance(strtod(argv[4], NULL), machineHeight));
                printf("%.10e\n", measure_height(strtod(argv[4], NULL), strtod(argv[5], NULL), machineHeight));
            } else {
                fprintf(stderr, "Angle is not from interval. See --help for user guide.\n");
                return 2;
            }
        } else {
            fprintf(stderr, "Height is not from interval. See --help for user guide.\n");
            return 2;
        }
    } else {
        fprintf(stderr, "Incorrect input. Use --help to get help.\n");
        return 1;
    }
    return 0;
}
