package ija.ija2018.homework2.common;

public interface Game {
    boolean placeFigure(Figure fig, Field field);
    boolean move(Figure figure, Field field);
    void undo();
}
