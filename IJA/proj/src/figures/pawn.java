package figures;

import java.util.ArrayList;
import app.*;
public class pawn extends chess_piece
{
    public pawn(boolean is_white) {
        super(is_white);
    }

    protected move_list[] get_piece_moves() {
        boolean isWhite = this.is_white;

        move_list[] moves = {};

        if(isWhite) {
            ArrayList<move_list> whiteMoves = new ArrayList<move_list>();
            whiteMoves.add(move_list.UP);
            whiteMoves.add(move_list.UP_RIGHT);
            whiteMoves.add(move_list.UP_LEFT);
            if(!has_moved) {whiteMoves.add(move_list.DOUBLE_UP);}
            moves = whiteMoves.toArray(moves);
        }
        else
        {
            ArrayList<move_list> blackMoves = new ArrayList<move_list>();
            blackMoves.add(move_list.DOWN);
            blackMoves.add(move_list.DOWN_RIGHT);
            blackMoves.add(move_list.DOWN_LEFT);
            if(!has_moved) {blackMoves.add(move_list.DOUBLE_DOWN);}
            moves = blackMoves.toArray(moves);
        }

        return moves;
    }

    protected boolean uses_single_move(){
        return true;
    }

    protected String get_name(){
        return "pawn";
    }

    protected String get_initial(){
        return "P";
    }
}
