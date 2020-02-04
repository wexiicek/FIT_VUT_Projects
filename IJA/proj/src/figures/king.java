package figures;
import app.*;
public class king extends chess_piece {
    public king(boolean is_white) {
        super(is_white);
    }

    protected move_list[] get_piece_moves() {
        move_list[] m = {
            move_list.UP,
            move_list.UP_RIGHT,
            move_list.RIGHT,
            move_list.DOWN_RIGHT,
            move_list.DOWN,
            move_list.DOWN_LEFT,
            move_list.LEFT,
            move_list.UP_LEFT
        };
        return m;
    }

    protected boolean uses_single_move(){
        return true;
    }

    protected String get_name(){
        return "king";
    }

    protected String get_initial(){
        return "K";
    }
}
