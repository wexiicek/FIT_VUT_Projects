package ija.ija2018.homework2;

import ija.ija2018.homework2.common.*;
import ija.ija2018.homework2.game.Board;

public abstract class GameFactory {

    public static Game createChessGame(Board board) {

         Game game = new Game_imp(board, true);
        /*
        * white figures on row 1 and 2
        *   Figures fill row 2
        *   rooks on first and last column on row 1
        * black figures on row 7 and 8
        *   Figures fill row 7
        *   rooks on first and last column on row 8
        * rooks and Figures based on chess rules
        * */
        board.getField(1,1).put(new Game_Figure(true, true));
        board.getField(8,1).put(new Game_Figure(true, true));
        for (int i = 1; i <= board.getSize(); i++) {
            board.getField(i, 2).put(new Game_Figure(true, false));
        }

        board.getField(1,8).put(new Game_Figure(false, true));
        board.getField(8,8).put(new Game_Figure(false, true));
        for (int i = 1; i <= board.getSize(); i++) {
            board.getField(i, 7).put(new Game_Figure(false, false));
        }
        return game;
    }


    public static Game createCheckersGame(Board board){
        Game game = new Game_imp(board, false);
        for (int i = 1; i <= 8; i=i+2) {
            board.getField(i, 1).put(new Game_Figure(true, false));
        }
        for (int i = 2; i <= 8; i=i+2) {
            board.getField(i, 2).put(new Game_Figure(true, false));
        }
        return game;
    }
}
