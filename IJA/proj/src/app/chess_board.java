package app;

import figures.*;
import javafx.scene.control.ListView;
import javafx.scene.layout.GridPane;
import parser.parser;


public class chess_board extends GridPane {
    public boolean white_move = true;
    public tile[][] tiles = new tile[8][8];
    public tile active_tile = null;
    private parser parser;
    private ListView list_view;
    private String temp;
    private boolean played_by_player;
    private boolean active;

    public chess_board(boolean playerIsWhite, parser p, ListView l) {
        super();
        active = true;
        played_by_player = false;
        parser = p;
        list_view = l;
        for (int x = 0; x < tiles[0].length; x++) {
            for (int y = 0; y < tiles[1].length; y++) {
                boolean light = ( (x + y) % 2 != 0 );
                tiles[x][y] = new tile(light, x, y);

                if (playerIsWhite) { this.add(tiles[x][y], x, 7 - y); }
                else { this.add(tiles[x][y], 7 - x, y); }

                final int xVal = x;
                final int yVal = y;
                tiles[x][y].setOnAction( e -> on_space_click(xVal, yVal) );
            }
        }
        this.initialize_board();
    }

    public void setActive(boolean state) {
        this.active = state;
    }

    public tile getSpace(int x, int y) {
        return tiles[x][y];
    }

    public void setActive_tile(tile s) {
        if (this.active_tile != null) {
            this.active_tile.getStyleClass().removeAll("chess-space-active");
        }

        this.active_tile = s;

        // Add style to new active space
        if (this.active_tile != null) {
            this.active_tile.getStyleClass().add("chess-space-active");
        }
    }

    public ListView getList_view() {
        return list_view;
    }

    public void setList_view(ListView list_view) {
        this.list_view = list_view;
    }

    public void setNonActiveSpace() {
        this.active_tile.getStyleClass().removeAll("chess-space-active");
        this.active_tile = null;
    }

    public tile getActive_tile() {
        return this.active_tile;
    }

    // prints location of all pieces on the board
    // TODO: Unfinished
    public String toString()
    {
        String pieceList = "";
        for (int i = 0; i < tiles[0].length; i++)
        {
            for (int j = 0; j < tiles[1].length; j++)
            {
                if (tiles[i][j].is_not_empty())
                {
                    //pieceList += spaces[i][j].toString();
                }
            }
        }
        return pieceList;
    }

    //define the starting piece positions
    public void initialize_board() {
        int white_row = 0;
        int black_row = 7;
        this.tiles[0][white_row].setPiece( new rook(true) );
        this.tiles[1][white_row].setPiece( new knight(true) );
        this.tiles[2][white_row].setPiece( new bishop(true) );
        this.tiles[3][white_row].setPiece( new queen (true) );
        this.tiles[4][white_row].setPiece( new king(true) );
        this.tiles[5][white_row].setPiece( new bishop(true) );
        this.tiles[6][white_row].setPiece( new knight(true) );
        this.tiles[7][white_row].setPiece( new rook  (true) );

        this.tiles[0][black_row].setPiece( new rook(false) );
        this.tiles[1][black_row].setPiece( new knight(false) );
        this.tiles[2][black_row].setPiece( new bishop(false) );
        this.tiles[3][black_row].setPiece( new queen (false) );
        this.tiles[4][black_row].setPiece( new king  (false) );
        this.tiles[5][black_row].setPiece( new bishop(false) );
        this.tiles[6][black_row].setPiece( new knight(false) );
        this.tiles[7][black_row].setPiece( new rook  (false) );

        for (int i = 0; i < this.tiles[0].length; i++) {
            this.tiles[i][1].setPiece(new pawn(true));
            this.tiles[i][6].setPiece( new pawn(false) );
        }
    }

    public void on_space_click(int x, int y) {
        if (active) {
            if (active_tile != null && active_tile.get_piece() != null) {
                move p = new move(active_tile.getX(), active_tile.getY(), x, y);
                this.process_game_move(p, true);

                this.setActive_tile(null);
            } else {
                if (tiles[x][y].get_piece() != null) {
                    this.setActive_tile(tiles[x][y]);
                }
            }
        }
    }



    // Process a app.move after it has been made by a player
    protected boolean process_game_move(move p, boolean user)
    {
        //System.out.println(parser.getInput());
        if (!played_by_player && user ) {
            if (parser.getCurrent_step() > 0) {
                try {
                    String str_update = parser.getInput();
                    System.out.println(str_update);
                    int position = str_update.indexOf((parser.getCurrent_step() + 1) + ".");
                    System.out.println("Pos: " + position);
                    str_update = str_update.substring(0, position);
                    System.out.println(str_update);
                    parser.getMoves_array().clear();
                    list_view.getItems().clear();
                    list_view.getItems().removeAll();
                    parser.parse_input(str_update);
                    for (String[] item : parser.getMoves_array()) {
                        list_view.getItems().add(item[0]);
                    }
                }
                catch (StringIndexOutOfBoundsException e) {
                    System.out.println("dont crash pls");
                }
            }
            played_by_player = true;
        }
        if (check_move_validity(p)) {
            if (this.white_move){
                if (!this.return_figure(p.oldX, p.oldY).is_white()){
                    return false;
                }
                this.white_move = false;
            }
            else {
                if (this.return_figure(p.oldX, p.oldY).is_white()){
                    return false;
                }
                this.white_move = true;
            }
            tile oldSpace = tiles[p.get_old_x()][p.get_old_y()];
            tile newSpace = tiles[p.get_new_x()][p.get_new_y()];

            chess_piece temp_piece = oldSpace.get_piece();
            boolean is_x = newSpace.get_piece() != null;
            if (user) {
                if (temp_piece.get_color().equals("white")) {
                    temp = (parser.getCurrent_step()+1) + ". ";
                    if (!temp_piece.get_initial().equals("P")){
                        temp = temp + temp_piece.get_initial();
                    }
                    temp = temp + num_to_row((oldSpace.getX())) + (oldSpace.getY() + 1);
                    if (is_x) {
                        temp = temp + "x";
                        /*if (!newSpace.get_piece().get_name().equals("pawn")){
                            temp = temp + newSpace.get_piece().get_initial();
                        }*/
                    }
                    temp = temp + num_to_row(newSpace.getX()) + (newSpace.getY() + 1) + " ";
                } else {
                    if (!temp_piece.get_initial().equals("P")){
                        temp = temp + temp_piece.get_initial();
                    }
                    temp = temp + num_to_row((oldSpace.getX())) + (oldSpace.getY() + 1);
                    if (is_x) {
                        temp = temp + "x";
                        /*if (!newSpace.get_piece().get_name().equals("pawn")){
                            temp = temp + newSpace.get_piece().get_initial();
                        }*/
                    }
                    temp = temp + num_to_row(newSpace.getX()) + (newSpace.getY() + 1);
                    parser.setInput(parser.getInput() + temp + "\n");
                    list_view.getItems().add(temp);
                    parser.setCurrent_step(parser.getCurrent_step()+1);
                }
                //TODO add data to parser
            }
            newSpace.setPiece( oldSpace.empty_tile() );

            return true;
        }
        else {
            return false;
        }
    }

    public boolean check_move_validity(move p) {
        tile oldSpace;
        tile newSpace;
        chess_piece piece;
        move_list[] moves;

        if (p == null) { return false; }

        try { oldSpace = tiles[p.get_old_x()][p.get_old_y()]; }
        catch (NullPointerException e) { return false; }

        try { newSpace = tiles[p.get_new_x()][p.get_new_y()]; }
        catch (NullPointerException e) { return false; }

        if (!oldSpace.is_not_empty()) { return false; }

        piece = oldSpace.get_piece();
        moves = piece.get_piece_moves();
        boolean matchesPieceMoves = false;

        int multiMoveCount;
        int stretchedMoveX;
        int stretchedMoveY;

        //labels this loop to break out later
        MoveLoop:
        for (move_list m : moves)
        {//iterates through multiple times if has multiple possible moves
            multiMoveCount = 1;
            if(!piece.uses_single_move()) {
                multiMoveCount = 8;
            }

            boolean hasCollided = false;

            for(int c = 1; c <= multiMoveCount; c++) {
                if (hasCollided){break;}

                stretchedMoveX = m.getX() * c;
                stretchedMoveY = m.getY() * c;

                tile tempSpace;

                try
                {
                    tempSpace = tiles[p.get_old_x() + stretchedMoveX]
                            [p.get_old_y() + stretchedMoveY];
                }
                catch (Exception e) { break; }

                //handles piece collision and capturing
                if(tempSpace.is_not_empty())
                {
                    hasCollided = true;
                    boolean piecesSameColor = tempSpace.get_piece().get_color() == oldSpace.get_piece().get_color();
                    //stops checking this app.move if pieces are the same color
                    if (piecesSameColor){ break; }
                }

                //if stretched app.move matches made app.move
                if ( p.x_gap() == stretchedMoveX && p.y_gap() == stretchedMoveY)
                {
                    matchesPieceMoves = true;

                    if (!pawn_move_validity_check(p)) {
                        return false;
                    }

                    piece.setHas_moved(true);
                    //breaks out of MoveLoop (both loops)
                    break MoveLoop;
                }
            }
        }
        return matchesPieceMoves;
    }

    private boolean pawn_move_validity_check(move p) {
        tile oldSpace = tiles[p.get_old_x()][p.get_old_y()];
        tile newSpace = tiles[p.get_new_x()][p.get_new_y()];
        chess_piece piece = oldSpace.get_piece();

        if ( !piece.get_name().equals("figures.pawn")) {
            return true;
        }

        if (p.x_gap() == 0) {
            int colorMod = p.y_gap() / Math.abs(p.y_gap());

            for(int c = 1; c <= Math.abs(p.y_gap()); c++) {
                if (  tiles[p.get_old_x()][p.get_old_y() + (c * colorMod)].is_not_empty()  ) {
                    return false;
                }
            }
        }
        else {
            return (newSpace.is_not_empty()) && piece.get_color() != newSpace.get_piece().get_color();
        }

        return true;
    }

    private chess_piece return_figure(int col, int row) {
        return this.tiles[col][row].get_piece();
    }

    private String num_to_row(int num){
        return String.valueOf((char)(num+97));
    }

    public boolean isPlayed_by_player() {
        return played_by_player;
    }
}
