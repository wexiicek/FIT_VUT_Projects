package ija.ija2018.homework2.common;

public interface Figure {
    boolean isWhite();
    boolean isRook();
    void setColAndRow(int col, int row, Field field);
    void setMyField(Field field);
    boolean equals (java.lang.Object obj);
    int hashCode();
    int getCol();
    int getRow();
    Field getMyField();
    String getPos();
    boolean move(Field moveTo);
    String getState();
}
