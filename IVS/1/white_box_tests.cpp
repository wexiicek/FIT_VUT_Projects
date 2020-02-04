//======== Copyright (c) 2017, FIT VUT Brno, All rights reserved. ============//
//
// Purpose:     White Box - Tests suite
//
// $NoKeywords: $ivs_project_1 $white_box_code.cpp
// $Author:     JMENO PRIJMENI <xlogin00@stud.fit.vutbr.cz>
// $Date:       $2017-01-04
//============================================================================//
/**
 * @file white_box_tests.cpp
 * @author JMENO PRIJMENI
 * 
 * @brief Implementace testu prace s maticemi.
 */

#include "gtest/gtest.h"
#include "white_box_code.h"

//============================================================================//
// ** ZDE DOPLNTE TESTY **
//
// Zde doplnte testy operaci nad maticemi. Cilem testovani je:
// 1. Dosahnout maximalniho pokryti kodu (white_box_code.cpp) testy.
// 2. Overit spravne chovani operaci nad maticemi v zavislosti na rozmerech 
//    matic.
//============================================================================//

class testingMatrix : public ::testing::Test {
protected:
	Matrix *emptyMatrix;
	Matrix *nonEmptyMatrix;


	void createEmptyMatrix() {
		emptyMatrix = new Matrix();	
	}	

	void insertValuesIntoMatrix() {
		int values[4] = {5, 6, 7, 9};
		nonEmptyMatrix->set(0, 0, values[0]);
		nonEmptyMatrix->set(1, 0, values[1]);
		nonEmptyMatrix->set(0, 1, values[2]);
		nonEmptyMatrix->set(1, 1, values[3]);
		/*
		l 5 l 7 l
		l---l---l
		l 6 l 9 l
		*/
	}
	
	Matrix createNonEmptyMatrix() {
		nonEmptyMatrix = new Matrix(2, 2);
		insertValuesIntoMatrix();
		return *nonEmptyMatrix;
	}

};

TEST_F(testingMatrix, Matrix) {
	EXPECT_NO_THROW(Matrix());
	EXPECT_NO_THROW(Matrix(42, 69));
	EXPECT_ANY_THROW(Matrix(-1,21));	
	EXPECT_ANY_THROW(Matrix(0,0));
	EXPECT_ANY_THROW(Matrix(1,0));
}			

TEST_F (testingMatrix, Set) {
	createNonEmptyMatrix();
	EXPECT_FALSE(nonEmptyMatrix->set(-1, -1, 40));
	EXPECT_FALSE(nonEmptyMatrix->set(40,40,40));
	EXPECT_TRUE(nonEmptyMatrix->set(0,0,1));
	EXPECT_TRUE(nonEmptyMatrix->set(1,0,1));
}

TEST_F (testingMatrix, SetVector) {
	createNonEmptyMatrix();
	EXPECT_TRUE(nonEmptyMatrix->set(std::vector<std::vector< double > > {{1,2},{3,4},}));
	EXPECT_FALSE(nonEmptyMatrix->set(std::vector<std::vector< double > > {{1,2},{3,4},{4,5},}));
}

TEST_F (testingMatrix, Get) {
	createNonEmptyMatrix();
	EXPECT_EQ(7, nonEmptyMatrix->get(0,1));
	EXPECT_ANY_THROW(nonEmptyMatrix->get(44,45));
}

TEST_F (testingMatrix, matrixEqual) {
	Matrix m1 = createNonEmptyMatrix();
	Matrix m2 = createNonEmptyMatrix();

	EXPECT_TRUE(m1 == m2);

	m1.set(0,0,0);
	m2.set(0,0,1);

	EXPECT_FALSE(m1 == m2);

	Matrix m3 = Matrix(3,3);
	m3.set(std::vector<std::vector< double > > {{5,2,4},{3,4,5},{4,5,7},});

	EXPECT_ANY_THROW(m2 == m3);

}

TEST_F (testingMatrix, matrixAddition) {
	createNonEmptyMatrix();
	createEmptyMatrix();

	EXPECT_ANY_THROW((*nonEmptyMatrix + *emptyMatrix));

	EXPECT_NO_THROW((*nonEmptyMatrix + *nonEmptyMatrix));
	Matrix final = (*nonEmptyMatrix + *nonEmptyMatrix);

	EXPECT_EQ(10, final.get(0, 0));
	EXPECT_EQ(12, final.get(1, 0));
	EXPECT_EQ(14, final.get(0, 1));
	EXPECT_EQ(18, final.get(1, 1));

	final = (final + *nonEmptyMatrix);

	EXPECT_EQ(15, final.get(0, 0));
	EXPECT_EQ(18, final.get(1, 0));
	EXPECT_EQ(21, final.get(0, 1));
	EXPECT_EQ(27, final.get(1, 1));
}

TEST_F (testingMatrix, matrixMultiplication) {
	createNonEmptyMatrix();
	createEmptyMatrix();

	EXPECT_ANY_THROW((*nonEmptyMatrix * *emptyMatrix));

	EXPECT_NO_THROW((*nonEmptyMatrix * *nonEmptyMatrix));
	Matrix final = (*nonEmptyMatrix * *nonEmptyMatrix);


	EXPECT_EQ(67.0, final.get(0, 0));
	EXPECT_EQ(84.0, final.get(1, 0));
	EXPECT_EQ(98.0, final.get(0, 1));
	EXPECT_EQ(123.0, final.get(1, 1));

	final = (final * *nonEmptyMatrix);

	EXPECT_EQ(923.0, final.get(0, 0));
	EXPECT_EQ(1158.0, final.get(1, 0));
	EXPECT_EQ(1351.0, final.get(0, 1));
	EXPECT_EQ(1695.0, final.get(1, 1));	

}

TEST_F (testingMatrix, matrixMultiplicationWithNumber) {
	createNonEmptyMatrix();
	Matrix final = (*nonEmptyMatrix * 3);

	EXPECT_EQ(15.0, final.get(0, 0));
	EXPECT_EQ(18.0, final.get(1, 0));
	EXPECT_EQ(21.0, final.get(0, 1));
	EXPECT_EQ(27.0, final.get(1, 1));	

	final = final * 2;

	EXPECT_EQ(30.0, final.get(0, 0));
	EXPECT_EQ(36.0, final.get(1, 0));
	EXPECT_EQ(42.0, final.get(0, 1));
	EXPECT_EQ(54.0, final.get(1, 1));	
	
}

TEST_F (testingMatrix, equationSolving) {
	createNonEmptyMatrix();
	std::vector<double> expectedSolution = {-5.0/3, 4.0/3};
	std::vector<double> b = {1, 2};

	EXPECT_EQ(expectedSolution, nonEmptyMatrix->solveEquation(b));

	Matrix test = Matrix(3,3);
	test.set(std::vector<std::vector< double > > {{5,2,4},{3,4,5},{4,5,7},});
	b = {1,1,1};
	expectedSolution = {1.0/3, 5.0/9, -4.0/9};
	EXPECT_EQ(expectedSolution, test.solveEquation(b));

	test = Matrix(2,3);
	test.set(std::vector<std::vector< double > > {{5,2,4},{3,4,5},});
	EXPECT_ANY_THROW(test.solveEquation(b));

	b = {1,1};
	test.set(std::vector<std::vector< double > > {{5,2,4},{3,4,5},});
	EXPECT_ANY_THROW(test.solveEquation(b));

	Matrix singular = Matrix(2,2);
	singular.set(std::vector<std::vector< double > > {{1,1},{1,1},});
	EXPECT_ANY_THROW(singular.solveEquation(b));

}

TEST_F (testingMatrix, Determinant) {
	Matrix one = Matrix(1,1);
	one.set(0,0,42);
	std::vector<double> b = {42};
	std::vector<double> expectedSolution = {1};

	EXPECT_EQ(expectedSolution, one.solveEquation(b));

	Matrix four = Matrix(4,4);
	b = {1,1,1,1};
	expectedSolution = {1,1,1,1};
	four.set(std::vector<std::vector< double > > {{1,0,0,0},{0,1,0,0},{0,0,1,0},{0,0,0,1},});

	std::vector<double> res = four.solveEquation(b);
	EXPECT_EQ(res, expectedSolution);
}

TEST_F (testingMatrix, Shapes) {
	Matrix matrix_1x1 = Matrix(1,1);
	Matrix matrix_4x2 = Matrix(4,2);
	Matrix matrix_2x4 = Matrix(2,4);
	Matrix matrix_5x1 = Matrix(5,1);
	Matrix matrix_1x5 = Matrix(1,5);
	Matrix matrix_4x4 = Matrix(4,4);

	EXPECT_TRUE(matrix_4x4.set(std::vector<std::vector< double > > {{1,0,0,0},{0,1,0,0},{0,0,1,0},{0,0,0,1},}));	
	EXPECT_TRUE(matrix_1x5.set(std::vector<std::vector< double > > {{1,8,3,5,4},}));
	EXPECT_TRUE(matrix_5x1.set(std::vector<std::vector< double > > {{8},{5},{4},{1},{1},}));
	EXPECT_TRUE(matrix_2x4.set(std::vector<std::vector< double > > {{1,3,6,9},{8,1,1,5},}));
	EXPECT_TRUE(matrix_4x2.set(std::vector<std::vector< double > > {{4,1},{9,4},{7,8},{1,2},}));
	EXPECT_TRUE(matrix_1x1.set(std::vector<std::vector< double > > {{5},}));

	EXPECT_FALSE(matrix_4x4.set(std::vector<std::vector< double > > {{1,0,0,0},{0,1,0,0},{1},{0,0,1,0},{0,0,0,1},}));	
	EXPECT_FALSE(matrix_1x5.set(std::vector<std::vector< double > > {{1,8,3,5,4},{38},{49},}));
	EXPECT_FALSE(matrix_5x1.set(std::vector<std::vector< double > > {{8},{5},{4},{1},{1},{42},}));
	EXPECT_FALSE(matrix_2x4.set(std::vector<std::vector< double > > {{1,3,6,9},{5,15,18,1},{8,1,1,5},}));
	EXPECT_FALSE(matrix_4x2.set(std::vector<std::vector< double > > {{4,1},{2,4},{9,4},{7,8},{1,2},}));
	EXPECT_FALSE(matrix_1x1.set(std::vector<std::vector< double > > {{5},{4},}));


}



/*** Konec souboru white_box_tests.cpp ***/
