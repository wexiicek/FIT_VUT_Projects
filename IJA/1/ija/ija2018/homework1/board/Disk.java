package ija.ija2018.homework1.board;

public class Disk {

    private final boolean isWhite;
    private Field myField;
    private int col;
    private int row;
    
    public Disk(boolean isWhite) {
        this.isWhite = isWhite;        
    }

    public boolean isWhite() {
        return this.isWhite == true;
    }
    
    public void setColAndRow(int col, int row, Field field) {
        this.myField = field;
        this.col = col;
        this.row = row;
    }

    @Override
    public boolean equals (java.lang.Object obj) {
        if (obj instanceof Disk) {
            Disk tmp = (Disk) obj;
            return (this.isWhite == tmp.isWhite);
        }
        return false;
    }

    @Override
    public int hashCode() {
        int hash = 4;
        int dec = this.isWhite ? 1 : 0;
        return hash * 6 + dec;
        
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

}