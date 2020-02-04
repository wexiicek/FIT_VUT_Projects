package app;

import javafx.scene.image.Image;

public abstract class chess_piece {
    protected boolean has_moved;
    protected boolean is_white;
    protected Image image;

    public chess_piece(boolean color)
    {
        this.is_white = color;

        has_moved = false;

        String location = "file:lib/assets/";
        String filename = this.get_color() + "_" + this.get_name() + ".png";
        //System.out.println(location + filename);
        this.image = new Image(location + filename);


    }

    public boolean getHas_moved() {
        return this.has_moved;
    }

    public void setHas_moved(boolean shouldBeTrue) {
        this.has_moved = shouldBeTrue;
    }

    // Returns image of chess piece
    public Image get_image() {
        return this.image;
    }

    public String get_color() {
        if (this.is_white == true) {
            return "white";
        }
        else {
            return "black";
        }
    }

    // returns true if color is white
    public boolean is_white() {
        return this.is_white;
    }

    public String toString() {
        return (this.get_name() + " " + this.get_color());
    }

    protected abstract String get_name();
    protected abstract String get_initial();
    protected abstract move_list[] get_piece_moves();
    protected abstract boolean uses_single_move();
}
