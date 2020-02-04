package ija.ija2018.homework2.common;

public class Game_Figure implements Figure {
    public final boolean isRook;
    public final boolean isWhite;
    public Field myField;
    public int col;
    public int row;
    
    public Game_Figure(boolean isWhite, boolean isRook) {
        this.isWhite = isWhite;
        this.isRook = isRook;
    }

    public Game_Figure(Figure figure) {
        this.isWhite = figure.isWhite();
        this.isRook = figure.isRook();
        this.col = figure.getCol();
        this.row = figure.getRow();
        this.myField = figure.getMyField();
    }

    public boolean isWhite() {
        return this.isWhite == true;
    }
    public boolean isRook() {
        return this.isRook == true;
    }
    
    public void setColAndRow(int col, int row, Field field) {
        setMyField(field);
        this.col = col;
        this.row = row;
    }

    public void setMyField(Field field) {
        this.myField = field;
    }

    @Override
    public boolean equals (java.lang.Object obj) {
        if (obj instanceof Figure) {
            Figure tmp = (Figure) obj;
            return (this.isWhite == tmp.isWhite());
        }
        return false;
    }

    @Override
    public int hashCode() {
        int hash = 4;
        int dec = this.isWhite ? 1 : 0;
        return hash * 6 + dec;
        
    }

    public int getCol() {
        return col;
    }

    public int getRow() {
        return row;
    }

    public Field getMyField() {
        if (this.myField != null) {
            return this.myField;
        }
        return null;
    }

    public String getPos(){
        int col = getCol();
        int row = getRow();
        return ""+col+":"+""+row;
    }

    public boolean move(Field moveTo) {
       if (moveTo != null) {
           if (this.row != moveTo.getRow() && this.col == moveTo.getCol()){
               int diff = this.row - moveTo.getRow();
               Field tmp = moveTo;
               if (diff > 0) {
                   while (tmp.getRow() != this.row){
                       if (tmp.get() != null){
                           return false;
                       }
                       tmp = tmp.nextField(Field.Direction.U);
                   }
                   this.myField.remove(this);
                   moveTo.put(this);
                   return true;
               }
               else {                     
                   while (tmp.getRow() != this.row){
                       if (tmp.get() != null){
                           return false;
                       }
                       tmp = tmp.nextField(Field.Direction.D);
                   }
                   this.myField.remove(this);
                   moveTo.put(this);
                   return true;
               }
           }
           
            if (this.col != moveTo.getCol() && this.row == moveTo.getRow()){
               int diff = this.col - moveTo.getCol();
               Field tmp = moveTo;
               if (diff > 0) {
                   while (tmp.getCol() != this.col){
                       if (tmp.get() != null){
                           return false;
                       }
                       tmp = tmp.nextField(Field.Direction.R);
                   }
                   this.myField.remove(this);
                   moveTo.put(this);
                   return true;
               }
               else {                     
                   while (tmp.getCol() != this.col){
                       if (tmp.get() != null){
                           return false;
                       }
                       tmp = tmp.nextField(Field.Direction.L);
                   }
                   this.myField.remove(this);
                   moveTo.put(this);
                   return true;
               }
           }
       }
       return false;
    }

    public String getState() {
        String color;
        if (isWhite()){
            color = "W";
        }
        else {
            color = "B";
        }
        if (isRook()){
            return "V[" + color + "]" + getPos();
        }
        return "P[" + color + "]" + getPos();
    }


}