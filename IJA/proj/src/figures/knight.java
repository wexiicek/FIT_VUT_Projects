package figures;
import app.*;
public class knight extends chess_piece
{
    public knight(boolean is_white) {
        super(is_white);
    }

    protected move_list[] get_piece_moves() {
        move_list[] m = {
            move_list.KNIGHT_LEFT_UP,
            move_list.KNIGHT_UP_LEFT,
            move_list.KNIGHT_UP_RIGHT,
            move_list.KNIGHT_RIGHT_UP,
            move_list.KNIGHT_RIGHT_DOWN,
            move_list.KNIGHT_DOWN_RIGHT,
            move_list.KNIGHT_DOWN_LEFT,
            move_list.KNIGHT_LEFT_DOWN
        };
        return m;
    }

    protected boolean uses_single_move(){
        return true;
    }

    protected String get_name(){
        return "knight";
    }

    protected String get_initial(){
        return "J";
    }
}
