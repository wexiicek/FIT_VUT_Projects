package app;

import javafx.scene.control.Button;
import javafx.scene.image.ImageView;

public class tile extends Button {
    private int x;
    private int y;
    private chess_piece piece; // piece currently on space

    public tile(boolean light, int x, int y) {
        super();
        this.x = x;
        this.y = y;
        this.piece = null;
        this.getStyleClass().add("chess-space");

        if (light) {
            this.getStyleClass().add("chess-space-light");
        }
        else {
            this.getStyleClass().add("chess-space-dark");
        }
    }

    public boolean is_not_empty() {
        return (this.piece != null);
    }

    public chess_piece empty_tile() {
        chess_piece tmpPiece = this.piece;
        setPiece(null);
        return tmpPiece;
    }

    public chess_piece get_piece() {
        return this.piece;
    }

    public void setPiece(chess_piece piece) {
        this.piece = piece;

        if (this.piece != null) {
            this.setGraphic(new ImageView(piece.get_image()));
        }
        else {
            this.setGraphic(new ImageView());
        }
    }

    public int getX() {
        return x;
    }

    public void setX(int x) {
        this.x = x;
    }

    public int getY() {
        return y;
    }

    public void setY(int y) {
        this.y = y;
    }
}
