package figures;
import app.*;

public class bishop extends chess_piece {
    public bishop(boolean is_white){
        super(is_white);
    }

    protected move_list[] get_piece_moves() {
        move_list[] m = {
            move_list.UP_RIGHT,
            move_list.DOWN_RIGHT,
            move_list.DOWN_LEFT,
            move_list.UP_LEFT
        };
        return m;
    }

    protected boolean uses_single_move() {
        return false;
    }

    protected String get_name() {
        return "bishop";
    }

    protected String get_initial(){
        return "S";
    }
}
