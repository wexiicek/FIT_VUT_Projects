package ija.ija2018.homework2.common;

import ija.ija2018.homework2.common.Figure;
import ija.ija2018.homework2.common.Field;
import ija.ija2018.homework2.game.Board;

public class BoardField implements Field {

    private final int row;
    private final int col;
    private Figure Figure;
    private final Field[] neighbors;

    public BoardField(int col, int row) {
        this.row = row;
        this.col = col;
        this.Figure = null;
        this.neighbors = new Field[9];
        for (int i = 0; i < 9; i++) {
            this.neighbors[i] = null;
        }
    }

    @Override
    public int hasFigure() {
        if (this.Figure == null) {
            return 0;
        }
        return 1;
    }

    public void rmFig() {
        if (this.Figure != null) {
            this.Figure = null;
        }
    }
    
    @Override
    public int getRow() {
        return this.row;
    }
    
    @Override
    public int getCol() {
        return this.col;
    }

    @Override
    public void addNextField(Field.Direction dirs, Field field) {
        neighbors[dirs.ordinal()] = field;
    }

    @Override
    public Field nextField(Field.Direction dirs) {
        return neighbors[dirs.ordinal()];
    }

    @Override
    public boolean put(Figure Figure) {
        if (this.Figure == null) {
            this.Figure = Figure;
            this.Figure.setColAndRow(this.col, this.row, this);
            return true;
        }
        return false;
    }

    @Override
    public boolean equals(java.lang.Object obj) {
        if (obj instanceof BoardField) {
            BoardField tmp = (BoardField) obj;
            if (this.row == tmp.row && this.col == tmp.col){
                if (tmp.Figure != null) {
                    return this.Figure.equals(tmp);
                }
                else {
                    return (this.Figure == null);
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    @Override
    public boolean remove(Figure Figure) {
        if (this.Figure.equals(Figure)) {
            this.Figure = null;
            return true;
        }
        return false;
    }

    @Override
    public Figure get() {
        return this.Figure;
    }

    @Override
    public boolean isEmpty() {
        return this.Figure == null;
    }



}