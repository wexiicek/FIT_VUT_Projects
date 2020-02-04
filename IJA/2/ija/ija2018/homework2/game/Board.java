package ija.ija2018.homework2.game;


import ija.ija2018.homework2.common.BoardField;
import ija.ija2018.homework2.common.Field;

public class Board {

    private final Field[][] board;
    private final int boardSize;

    public Board(int size) {
        this.boardSize = size;
        this.board = new Field[this.boardSize+2][this.boardSize+2];

        for (int row = this.boardSize +1 ; row >= 0; row--) {
            for (int col = 0; col  < this.boardSize + 1; col++) {
                this.board[col][row] = new BoardField(col, row);
            }
        }
        
        for (int row = this.boardSize; row >= 1; row--) {
            for (int col = 1; col <= this.boardSize; col++) {
                this.board[col][row].addNextField(Field.Direction.D, board[col][row-1]);
                this.board[col][row].addNextField(Field.Direction.L, board[col - 1][row]);
                this.board[col][row].addNextField(Field.Direction.LD, board[col - 1][row - 1]);
                this.board[col][row].addNextField(Field.Direction.LU, board[col - 1][row + 1]);
                this.board[col][row].addNextField(Field.Direction.R, board[col + 1][row]);
                this.board[col][row].addNextField(Field.Direction.RD, board[col + 1][row - 1]);
                this.board[col][row].addNextField(Field.Direction.RU, board[col + 1][row + 1]);
                this.board[col][row].addNextField(Field.Direction.U, board[col][row + 1]);
            }
        }
    }
    
    public Field getField(int col, int row) {
        if (col <= this.boardSize && col > 0 && row <= this.boardSize && row > 0) {
            return this.board[col][row];
        }
        return null;
    }

    public int getSize() {
        return this.boardSize;
    }

    public String printBoard() {
        String boardStr = "----------------\n 1 2 3 4 5 6 7 8\n";
        for (int row = this.getSize() ; row > 0; row--) {
            boardStr = boardStr + row;
            for (int col = 1; col  <= this.getSize(); col++) {
                Field temp = this.getField(col, row);
                if (temp.hasFigure() == 1){
                    if (temp.get().isRook()){
                        boardStr = boardStr + "R ";
                    }
                    else {
                    boardStr = boardStr + "P ";}
                }
                else {
                    boardStr = boardStr + "  ";
                }
            }
            boardStr = boardStr + "\n";
        }
        return boardStr;
    }

}