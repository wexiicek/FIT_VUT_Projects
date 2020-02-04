//======== Copyright (c) 2017, FIT VUT Brno, All rights reserved. ============//
//
// Purpose:     Red-Black Tree - public interface tests
//
// $NoKeywords: $ivs_project_1 $black_box_tests.cpp
// $Author:     JMENO PRIJMENI <xlogin00@stud.fit.vutbr.cz>
// $Date:       $2017-01-04
//============================================================================//
/**
 * @file black_box_tests.cpp
 * @author JMENO PRIJMENI
 * 
 * @brief Implementace testu binarniho stromu.
 */

#include <vector>

#include "gtest/gtest.h"

#include "red_black_tree.h"

//============================================================================//
// ** ZDE DOPLNTE TESTY **
//
// Zde doplnte testy Red-Black Tree, testujte nasledujici:
// 1. Verejne rozhrani stromu
//    - InsertNode/DeleteNode a FindNode
//    - Chovani techto metod testuje pro prazdny i neprazdny strom.
// 2. Axiomy (tedy vzdy platne vlastnosti) Red-Black Tree:
//    - Vsechny listove uzly stromu jsou *VZDY* cerne.
//    - Kazdy cerveny uzel muze mit *POUZE* cerne potomky.
//    - Vsechny cesty od kazdeho listoveho uzlu ke koreni stromu obsahuji
//      *STEJNY* pocet cernych uzlu.
//============================================================================//

class EmptyTree : public ::testing::Test {
protected:
	BinaryTree *emptyTestTree;

	void createEmptyTree () {
		emptyTestTree = new BinaryTree();
	}


};

class NonEmptyTree : public ::testing::Test {
protected:	

	BinaryTree *testTree;
	int values [9] = {4, 7, 8, 12, 14, 21, 22, 23, 37};

	void createNonEmptyTree () {
		testTree = new BinaryTree();		
			for (int value : values) {
			testTree->InsertNode(value);
		}
	}

};

class TreeAxioms : public ::testing::Test {
protected:
	BinaryTree *rbTree;

	int values [7] = {0, 1, 3, 5, 7, 9, 11};

	void createRbTree () {
		rbTree = new BinaryTree();
		for (int value: values){
			rbTree->InsertNode(value);
		}
	}
};

TEST_F(EmptyTree, InsertNode) {
	createEmptyTree();	

	EXPECT_TRUE(emptyTestTree->GetRoot() == NULL);

	std::pair<bool, BinaryTree::Node_t *> node1 = emptyTestTree->InsertNode(2);
	std::pair<bool, BinaryTree::Node_t *> node2 = emptyTestTree->InsertNode(2);

	EXPECT_TRUE(emptyTestTree->GetRoot() != NULL);
	EXPECT_TRUE(node1.first);
	EXPECT_FALSE(node2.first);

}

TEST_F(EmptyTree, DeleteNode) {

	createEmptyTree();

	EXPECT_FALSE(emptyTestTree->DeleteNode(1));
	EXPECT_FALSE(emptyTestTree->DeleteNode(6));

	std::pair<bool, BinaryTree::Node_t *> node1 = emptyTestTree->InsertNode(2);
	std::pair<bool, BinaryTree::Node_t *> node2 = emptyTestTree->InsertNode(3);

	EXPECT_TRUE(emptyTestTree->DeleteNode(2));
	EXPECT_TRUE(emptyTestTree->DeleteNode(3));

	EXPECT_TRUE(emptyTestTree->GetRoot() == NULL);

}

TEST_F(EmptyTree, FindNode) {

	createEmptyTree();

	std::pair<bool, BinaryTree::Node_t *> node1 = emptyTestTree->InsertNode(1);
	std::pair<bool, BinaryTree::Node_t *> node2 = emptyTestTree->InsertNode(2);
	std::pair<bool, BinaryTree::Node_t *> node3 = emptyTestTree->InsertNode(3);

	EXPECT_FALSE(emptyTestTree->FindNode(0) != NULL);
	EXPECT_FALSE(emptyTestTree->FindNode(4) != NULL);

	EXPECT_TRUE(emptyTestTree->FindNode(1) != NULL);
	EXPECT_TRUE(emptyTestTree->FindNode(2) != NULL);

	emptyTestTree->DeleteNode(2);

	EXPECT_FALSE(emptyTestTree->FindNode(2) != NULL);

}

TEST_F(NonEmptyTree, InsertNode) {

	createNonEmptyTree();

	EXPECT_FALSE(testTree->GetRoot() == NULL);

	std::pair<bool, BinaryTree::Node_t *> node1 = testTree->InsertNode(1);
	std::pair<bool, BinaryTree::Node_t *> node2 = testTree->InsertNode(3);
	std::pair<bool, BinaryTree::Node_t *> node3 = testTree->InsertNode(4);
 	
	EXPECT_TRUE(node1.first);
	EXPECT_TRUE(node2.first);
	EXPECT_FALSE(node3.first);

}

TEST_F(NonEmptyTree, DeleteNode) {

	createNonEmptyTree();

	EXPECT_FALSE(testTree->DeleteNode(0));
	EXPECT_TRUE(testTree->DeleteNode(4));

	std::pair<bool, BinaryTree::Node_t *> node1 = testTree->InsertNode(0);
	EXPECT_TRUE(testTree->DeleteNode(0));

}

TEST_F(NonEmptyTree, FindNode) {

	createNonEmptyTree();

	EXPECT_FALSE(testTree->FindNode(0));
	EXPECT_TRUE(testTree->FindNode(4));

	std::pair<bool, BinaryTree::Node_t *> node1 = testTree->InsertNode(0);
	EXPECT_TRUE(testTree->FindNode(0));
	EXPECT_TRUE(testTree->DeleteNode(0));
	EXPECT_FALSE(testTree->FindNode(0));

}

/*
// 2. Axiomy (tedy vzdy platne vlastnosti) Red-Black Tree:
//    - Vsechny listove uzly stromu jsou *VZDY* cerne.
//    - Kazdy cerveny uzel muze mit *POUZE* cerne potomky.
//    - Vsechny cesty od kazdeho listoveho uzlu ke koreni stromu obsahuji
//      *STEJNY* pocet cernych uzlu.
*/

TEST_F(TreeAxioms, Axiom1) {
	
	createRbTree();

	std::vector<BinaryTree::Node_t *> nodes;
	rbTree->GetLeafNodes(nodes);

	for (BinaryTree::Node_t *node : nodes) {
		EXPECT_EQ(BinaryTree::BLACK, node->color);
	}
}

TEST_F(TreeAxioms, Axiom2) {
	
	createRbTree();

	std::vector<BinaryTree::Node_t *> nodes;
	rbTree->GetAllNodes(nodes);

	for (BinaryTree::Node_t *node : nodes) {
		if (node->color == BinaryTree::RED){
			EXPECT_EQ(node->pLeft->color, BinaryTree::BLACK);
			EXPECT_EQ(node->pRight->color, BinaryTree::BLACK);
		}
	}
}

TEST_F(TreeAxioms, Axiom3) {
	
	createRbTree();

	std::vector<BinaryTree::Node_t *> nodes;
	rbTree->GetLeafNodes(nodes);

	int blackNodesPrevious = -1;

	for (BinaryTree::Node_t *node : nodes) {
		int blackNodesCurrent = 0;
		BinaryTree::Node_t *currentNode = node;

		while (currentNode) {
			if (currentNode->color == BinaryTree::BLACK) {
				blackNodesCurrent++;
			}
			BinaryTree::Node_t *parentNode = currentNode->pParent;
			currentNode = parentNode;
		}
		if (blackNodesPrevious >= 0) {
			EXPECT_EQ(blackNodesPrevious, blackNodesCurrent);
		}

		blackNodesPrevious = blackNodesCurrent; 
	}

}

/*** Konec souboru black_box_tests.cpp ***/
