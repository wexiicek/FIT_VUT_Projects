package figures;
import app.*;
public class rook extends chess_piece
{
    public rook(boolean is_white) {
        super(is_white);
    }

    protected move_list[] get_piece_moves() {
        move_list[] m = {
            move_list.UP,
            move_list.RIGHT,
            move_list.DOWN,
            move_list.LEFT
        };
        return m;
    }

    protected boolean uses_single_move(){
        return false;
    }

    protected String get_name(){
        return "rook";
    }

    protected String get_initial(){
        return "V";
    }
}
