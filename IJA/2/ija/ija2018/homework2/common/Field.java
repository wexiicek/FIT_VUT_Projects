package ija.ija2018.homework2.common;

import ija.ija2018.homework2.game.Board;

public interface Field {

    public static enum Direction {
        D, L, LD, LU, R, RD, RU, U;
    }

    int getRow();
    
    int getCol();
    
    int hasFigure();

    void addNextField(Field.Direction dirs, Field field);

    Field nextField(Field.Direction dirs);

    Figure get();

    boolean put(Figure Figure);

    boolean remove(Figure Figure);

    boolean isEmpty();

    void rmFig();



}