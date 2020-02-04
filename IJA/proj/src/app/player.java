package app;


import javafx.scene.control.Alert;
import javafx.scene.control.ListView;
import parser.parser;


public class player {
    private game_step step_white;
    private game_step step_black;
    private parser parser;
    private chess_board board;
    private boolean is_short_format;
    private ListView list_view;
    private MyRunnable mr;

    player() {
        step_white = new game_step();
        step_black = new game_step();
    }

    public void setMr(MyRunnable r) {
        this.mr = r;
    }

    public parser getParser() {
        return parser;
    }

    public void setIs_short_format(boolean is_short_format) {
        this.is_short_format = is_short_format;
    }



    public boolean initiate_player(chess_board brd){
        mr.setBoard(brd);
        if (parser.getMoves_array().size() > 0 && parser.getCurrent_step() < parser.getMove_count()) {
            parse_step(brd);
            make_a_move(step_white.getFrom_x(), step_white.getFrom_y(), step_white.getTo_x(), step_white.getTo_y(), brd);
            if (step_black.getFrom_x() != -1) {
                make_a_move(step_black.getFrom_x(), step_black.getFrom_y(), step_black.getTo_x(), step_black.getTo_y(), brd);
            }
            parser.setCurrent_step(parser.getCurrent_step()+1);

            board.getList_view().getSelectionModel().select(parser.getCurrent_step());
        }
        if (step_white.isCheck_mate()) {
            show_check_mate(true);
        }
        else if (step_black.isCheck_mate()){
            show_check_mate(false);
        }
        System.out.println(parser.getCurrent_step()-1 + " " + parser.getMoves_array().size());
        if (parser.getCurrent_step() == parser.getMoves_array().size()) {
            return true;
        }
        return false;
    }

    public void setParser(parser parser) {
        this.parser = parser;
    }

    public void setBoard(chess_board board) {
        this.board = board;
    }

    public void make_a_move(int from_x, int from_y, int to_x, int to_y, chess_board brd){
        System.out.println(parser.getCurrent_step()+". FROM: " + from_x + "," + from_y + "TO: " + to_x + "," + to_y);
        move mov = new move(from_x,from_y,to_x,to_y);
        tile space = brd.getSpace(to_x,to_y);
        brd.setActive_tile(space);
        brd.process_game_move(mov, false);
        brd.setNonActiveSpace();
    }

    private tile find_majesty_tile(boolean is_white, String king_or_queen, chess_board board) {
        chess_piece temp;
        for (int col = 0; col < 8; col++){
            for (int row = 0; row < 8; row++){
                try {
                    temp = board.getSpace(col, row).get_piece();
                    if (temp.get_name().equals(king_or_queen)){
                        if (temp.is_white() == is_white){
                            System.out.println(king_or_queen+" at "+col+","+row);
                            return board.getSpace(col, row);
                        }
                    }
                }
                catch (NullPointerException e) {
                    continue;
                }
            }
        }
        return null;
    }



    private tile find_figure_tile(boolean is_white, int row, String type, chess_board board) {
        //System.out.println(row_num);
        if (is_white) {
            if (type.equals("pawn")){
                for (int i = 0; i < 7; i++){
                    //System.out.println(row + " " + i);
                    try {
                        //System.out.println(board.getSpace(row, i).get_piece().get_name());
                        if ("pawn".equals(board.getSpace(row, i).get_piece().get_name())) {
                            return board.getSpace(row, i);
                        }
                    }
                    catch (NullPointerException e) {
                        System.out.println("kk " + i + " "+e);
                    }
                }
            }
        }
        else {
            if (type.equals("pawn")){
                for (int i = 0; i < 7; i++){
                    //System.out.println(row + " " + i);
                    try {
                        //System.out.println(board.getSpace(row, i).get_piece().get_name());
                        if ("pawn".equals(board.getSpace(row, i).get_piece().get_name())) {
                            if ("black".equals(board.getSpace(row, i).get_piece().get_color())) {
                                return board.getSpace(row, i);
                            }
                        }
                    }
                    catch (NullPointerException e) {
                        System.out.println("kk " + i + " "+e);
                    }
                }
            }
        }
        return null;
    }

    private void parse_step(chess_board board){
        if (parser.getCurrent_step() < 0) {
            parser.setCurrent_step(0); //hhotfix
        }
        String[] wht;
        if (is_short_format) {
            wht = parser.getMoves_array().get(parser.getCurrent_step());

            //white move
            if (parser.getMoves_array().get(parser.getCurrent_step())[1].length() == 2) {
                // pesec - pawn
                step_white.setFigure_type("pawn");
                step_white.setFrom_x(row_to_number(parser.getMoves_array().get(parser.getCurrent_step())[1].charAt(0)));
                tile from_white = find_figure_tile(true, step_white.getFrom_x(), step_white.getFigure_type(), board);
                step_white.setFrom_y(from_white.getY());
                step_white.setTo_y(char_to_num(parser.getMoves_array().get(parser.getCurrent_step())[1].charAt(1))-1);
                step_white.setTo_x(step_white.getFrom_x());
                //step_white.print_step();
            }

            else if (!Character.isUpperCase(parser.getMoves_array().get(parser.getCurrent_step())[1].charAt(0))) {
                tile pawn = find_pawn(true, row_to_number(wht[1].charAt(0)), board);
                step_white.setFrom_x(pawn.getX());
                step_white.setFrom_y(pawn.getY());
                step_white.setTo_x(row_to_number(wht[1].charAt(2)));
                step_white.setTo_y(char_to_num(wht[1].charAt(3))-1);
            }

            else {
                if (wht[1].charAt(0) == 'K'){
                    //kral - king
                    step_white.setFigure_type("king");
                    tile from_white = find_majesty_tile(true, "king", board);
                    step_white.setFrom_x(from_white.getX());
                    step_white.setFrom_y(from_white.getY());
                    int tmp_offset_k = 0;
                    if (wht[1].contains("x")){
                        tmp_offset_k += 1;
                    }
                    step_white.setTo_x(row_to_number(parser.getMoves_array().get(parser.getCurrent_step())[1].charAt(tmp_offset_k+1)));
                    step_white.setTo_y(char_to_num(parser.getMoves_array().get(parser.getCurrent_step())[1].charAt(tmp_offset_k+2))-1);
                }
                else if (wht[1].charAt(0) == 'D') {
                    //dama - queen
                    step_white.setFigure_type("king");
                    tile from_white = find_majesty_tile(true, "queen", board);
                    step_white.setFrom_x(from_white.getX());
                    step_white.setFrom_y(from_white.getY());
                    int tmp_offset_d = 0;
                    if (wht[1].contains("x")){
                        tmp_offset_d += 1;
                    }
                    step_white.setTo_x(row_to_number(parser.getMoves_array().get(parser.getCurrent_step())[1].charAt(tmp_offset_d+1)));
                    step_white.setTo_y(char_to_num(parser.getMoves_array().get(parser.getCurrent_step())[1].charAt(tmp_offset_d+2))-1);
                }

                else if (wht[1].charAt(0) == 'V') {
                    //vez
                    int offset_w_v = 0;
                    tile v1 = find_dual(true, null, "rook", board);
                    tile v2 = find_dual(true, v1, "rook", board);
                    if (wht[1].contains("x")){
                        offset_w_v += 1;
                    }
                    move temp = new move();
                    temp.set_old_x(v1.getX());
                    temp.set_old_y(v1.getY());
                    temp.set_new_x(row_to_number(wht[1].charAt(offset_w_v+1)));
                    temp.set_new_y(char_to_num(wht[1].charAt(offset_w_v+2))-1);
                    if (!board.check_move_validity(temp) && v2 != null) {
                        temp.set_old_x(v2.getX());
                        temp.set_old_y(v2.getY());
                        temp.set_new_x(row_to_number(wht[1].charAt(offset_w_v+1)));
                        temp.set_new_y(char_to_num(wht[1].charAt(offset_w_v+2))-1);
                    }
                    step_white.setFrom_x(temp.get_old_x());
                    step_white.setFrom_y(temp.get_old_y());
                    step_white.setTo_x(temp.get_new_x());
                    step_white.setTo_y(temp.get_new_y());

                }

                else if (wht[1].charAt(0) == 'J') {
                    //jezdec
                    int offset_w_k = 0;
                    tile k1 = find_dual(true, null, "knight", board);
                    tile k2 = find_dual(true, k1, "knight", board);
                    if (wht[1].contains("x")){
                        offset_w_k += 1;
                    }
                    move temp = new move();
                    temp.set_old_x(k1.getX());
                    temp.set_old_y(k1.getY());
                    temp.set_new_x(row_to_number(wht[1].charAt(offset_w_k+1)));
                    temp.set_new_y(char_to_num(wht[1].charAt(offset_w_k+2))-1);
                    if (!board.check_move_validity(temp) && k2 != null) {
                        temp.set_old_x(k2.getX());
                        temp.set_old_y(k2.getY());
                        temp.set_new_x(row_to_number(wht[1].charAt(offset_w_k+1)));
                        temp.set_new_y(char_to_num(wht[1].charAt(offset_w_k+2))-1);
                    }
                    step_white.setFrom_x(temp.get_old_x());
                    step_white.setFrom_y(temp.get_old_y());
                    step_white.setTo_x(temp.get_new_x());
                    step_white.setTo_y(temp.get_new_y());



                }

                else if (wht[1].charAt(0) == 'S') {
                    //strelec
                    int offset_w_b = 0;
                    tile b1 = find_dual(true, null, "bishop", board);
                    tile b2 = find_dual(true, b1, "bishop", board);
                    if (wht[1].contains("x")){
                        offset_w_b += 1;
                    }
                    move temp = new move();
                    temp.set_old_x(b1.getX());
                    temp.set_old_y(b1.getY());
                    temp.set_new_x(row_to_number(wht[1].charAt(offset_w_b+1)));
                    temp.set_new_y(char_to_num(wht[1].charAt(offset_w_b+2))-1);
                    if (!board.check_move_validity(temp) && b2 != null) {
                        temp.set_old_x(b2.getX());
                        temp.set_old_y(b2.getY());
                        temp.set_new_x(row_to_number(wht[1].charAt(offset_w_b+1)));
                        temp.set_new_y(char_to_num(wht[1].charAt(offset_w_b+2))-1);
                    }
                    step_white.setFrom_x(temp.get_old_x());
                    step_white.setFrom_y(temp.get_old_y());
                    step_white.setTo_x(temp.get_new_x());
                    step_white.setTo_y(temp.get_new_y());
                }
            }

            //black move
            if (parser.getMoves_array().get(parser.getCurrent_step())[2] == null){
                return;
            }
            if (parser.getMoves_array().get(parser.getCurrent_step())[2].length() == 2) {
                // pesec - pawn
                step_black.setFigure_type("pawn");
                step_black.setFrom_x(row_to_number(parser.getMoves_array().get(parser.getCurrent_step())[2].charAt(0)));
                tile from_black = find_figure_tile(false, step_black.getFrom_x(), step_black.getFigure_type(), board);
                step_black.setFrom_y(from_black.getY());
                step_black.setTo_y(char_to_num(parser.getMoves_array().get(parser.getCurrent_step())[2].charAt(1))-1);
                step_black.setTo_x(step_black.getFrom_x());
                //step_black.print_step();
            }

            else if (!Character.isUpperCase(parser.getMoves_array().get(parser.getCurrent_step())[2].charAt(0))) {
                tile pawn = find_pawn(false, row_to_number(wht[2].charAt(0)), board);
                step_black.setFrom_x(pawn.getX());
                step_black.setFrom_y(pawn.getY());
                step_black.setTo_x(row_to_number(wht[2].charAt(2)));
                step_black.setTo_y(char_to_num(wht[2].charAt(3))-1);
            }

            else {
                if (wht[2].charAt(0) == 'K'){
                    //kral - king
                    step_black.setFigure_type("king");
                    tile from_black = find_majesty_tile(false, "king", board);
                    step_black.setFrom_x(from_black.getX());
                    step_black.setFrom_y(from_black.getY());
                    int tmp_offset_k = 0;
                    if (wht[2].contains("x")){
                        tmp_offset_k += 1;
                    }
                    step_black.setTo_x(row_to_number(parser.getMoves_array().get(parser.getCurrent_step())[2].charAt(tmp_offset_k+1)));
                    step_black.setTo_y(char_to_num(parser.getMoves_array().get(parser.getCurrent_step())[2].charAt(tmp_offset_k+2))-1);
                }
                else if (wht[2].charAt(0) == 'D') {
                    //dama - queen
                    step_black.setFigure_type("king");
                    tile from_black = find_majesty_tile(false, "queen", board);
                    step_black.setFrom_x(from_black.getX());
                    step_black.setFrom_y(from_black.getY());
                    int tmp_offset_d = 0;
                    if (wht[2].contains("x")){
                        tmp_offset_d += 1;
                    }
                    step_black.setTo_x(row_to_number(parser.getMoves_array().get(parser.getCurrent_step())[2].charAt(tmp_offset_d+1)));
                    step_black.setTo_y(char_to_num(parser.getMoves_array().get(parser.getCurrent_step())[2].charAt(tmp_offset_d+2))-1);
                }

                else if (wht[2].charAt(0) == 'V') {
                    //vez
                    int offset_b_v = 0;
                    tile v1 = find_dual(false, null, "rook", board);
                    tile v2 = find_dual(false, v1, "rook", board);
                    if (wht[2].contains("x")){
                        offset_b_v += 1;
                    }
                    move temp = new move();
                    temp.set_old_x(v1.getX());
                    temp.set_old_y(v1.getY());
                    temp.set_new_x(row_to_number(wht[2].charAt(offset_b_v+1)));
                    temp.set_new_y(char_to_num(wht[2].charAt(offset_b_v+2))-1);
                    if (!board.check_move_validity(temp) && v2 != null) {
                        temp.set_old_x(v2.getX());
                        temp.set_old_y(v2.getY());
                        temp.set_new_x(row_to_number(wht[2].charAt(offset_b_v+1)));
                        temp.set_new_y(char_to_num(wht[2].charAt(offset_b_v+2))-1);
                    }
                    step_black.setFrom_x(temp.get_old_x());
                    step_black.setFrom_y(temp.get_old_y());
                    step_black.setTo_x(temp.get_new_x());
                    step_black.setTo_y(temp.get_new_y());

                }

                else if (wht[2].charAt(0) == 'J') {
                    //jezdec
                    int offset_b_k = 0;
                    tile k1 = find_dual(false, null, "knight", board);
                    tile k2 = find_dual(false, k1, "knight", board);
                    if (wht[2].contains("x")){
                        offset_b_k += 1;
                    }
                    move temp = new move();
                    temp.set_old_x(k1.getX());
                    temp.set_old_y(k1.getY());
                    temp.set_new_x(row_to_number(wht[2].charAt(offset_b_k+1)));
                    temp.set_new_y(char_to_num(wht[2].charAt(offset_b_k+2))-1);
                    if (!board.check_move_validity(temp) && k2 != null) {
                        temp.set_old_x(k2.getX());
                        temp.set_old_y(k2.getY());
                        temp.set_new_x(row_to_number(wht[2].charAt(offset_b_k+1)));
                        temp.set_new_y(char_to_num(wht[2].charAt(offset_b_k+2))-1);
                    }
                    step_black.setFrom_x(temp.get_old_x());
                    step_black.setFrom_y(temp.get_old_y());
                    step_black.setTo_x(temp.get_new_x());
                    step_black.setTo_y(temp.get_new_y());



                }

                else if (wht[2].charAt(0) == 'S') {
                    //strelec
                    int offset_b_b = 0;
                    tile b1 = find_dual(false, null, "bishop", board);
                    tile b2 = find_dual(false, b1, "bishop", board);
                    if (wht[2].contains("x")){
                        offset_b_b += 1;
                    }
                    move temp = new move();
                    temp.set_old_x(b1.getX());
                    temp.set_old_y(b1.getY());
                    temp.set_new_x(row_to_number(wht[2].charAt(offset_b_b+1)));
                    temp.set_new_y(char_to_num(wht[2].charAt(offset_b_b+2))-1);
                    if (!board.check_move_validity(temp) && b2 != null) {
                        temp.set_old_x(b2.getX());
                        temp.set_old_y(b2.getY());
                        temp.set_new_x(row_to_number(wht[2].charAt(offset_b_b+1)));
                        temp.set_new_y(char_to_num(wht[2].charAt(offset_b_b+2))-1);
                    }
                    step_black.setFrom_x(temp.get_old_x());
                    step_black.setFrom_y(temp.get_old_y());
                    step_black.setTo_x(temp.get_new_x());
                    step_black.setTo_y(temp.get_new_y());
                }
            }

            if (wht[1].indexOf('#') > 0) {
                step_white.setCheck_mate(true);
            }
            if (wht[2] != null) {
                if (wht[2].indexOf('#') > 0) {
                    step_black.setCheck_mate(true);
                }
            }
            // end of black move
        }
        else {
            String pattern = "([a-h][0-9])";
            wht = parser.getMoves_array().get(parser.getCurrent_step());
            int offset_w; int offset_b;
            if (Character.isUpperCase(wht[1].charAt(0))){
                //System.out.println("W uppercase");
                offset_w = 1;
            }
            else {
                offset_w = 0;
            }
            step_white.setFrom_x(row_to_number(wht[1].charAt(offset_w)));
            step_white.setFrom_y(char_to_num(wht[1].charAt(offset_w+1))-1);
            if (wht[1].charAt(offset_w+2) == 'x'){
                offset_w += 1;
            }
            step_white.setTo_x(row_to_number(wht[1].charAt(offset_w+2)));
            step_white.setTo_y(char_to_num(wht[1].charAt(offset_w+3))-1);

            if (wht[2] == null) {
                step_black.setFrom_x(-1);
            }
            else {

                if (Character.isUpperCase(wht[2].charAt(0))) {
                    offset_b = 1;
                } else {
                    offset_b = 0;
                }
                step_black.setFrom_x(row_to_number(wht[2].charAt(offset_b)));
                step_black.setFrom_y(char_to_num(wht[2].charAt(offset_b+1))-1);
                if (wht[2].charAt(offset_b+2) == 'x'){
                    offset_b += 1;
                }
                step_black.setTo_x(row_to_number(wht[2].charAt(offset_b+2)));
                step_black.setTo_y(char_to_num(wht[2].charAt(offset_b+3))-1);
            }
            if (wht[1].indexOf('#') > 0) {
                step_white.setCheck_mate(true);
            }
            if (wht[2] != null) {
                if (wht[2].indexOf('#') > 0) {
                    step_black.setCheck_mate(true);
                }
            }
        }

    }

    private tile find_pawn(boolean is_white, int col, chess_board board) {
        for (int row = 0; row < 8; row++) {
            try {
                if (board.getSpace(col, row).get_piece().get_name().equals("pawn") && board.getSpace(col, row).get_piece().is_white() == is_white) {
                    return board.getSpace(col, row);
                }
            }
            catch (NullPointerException e) {

            }
        }
        return null;
    }


    private tile find_dual(boolean is_white, tile cmp, String type, chess_board board) {
        chess_piece temp;
        for (int col = 0; col < 8; col++){
            for (int row = 0; row < 8; row++){
                try {
                    temp = board.getSpace(col, row).get_piece();
                    if (temp.get_name().equals(type)){
                        if (temp.is_white() == is_white && !board.getSpace(col, row).equals(cmp)){
                            return board.getSpace(col, row);
                        }
                    }
                }
                catch (NullPointerException e) {
                    continue;
                }
            }
        }
        return null;
    }

    private void show_check_mate(boolean is_white) {
        board.setActive(false);
        Alert game_end = new Alert(Alert.AlertType.INFORMATION);
        String winner = is_white ? "White" : "Black";
        game_end.setHeaderText(winner + " player won.");
        game_end.setTitle("Game over");
        game_end.showAndWait();
    }

    private int row_to_number(char row) {
        return Integer.parseInt(String.valueOf((char)(row-49)));
    }

    public int char_to_num(char num) {
        return Integer.parseInt(String.valueOf(num));
    }

}
