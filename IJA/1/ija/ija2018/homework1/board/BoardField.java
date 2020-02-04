package ija.ija2018.homework1.board;

public class BoardField implements Field {

    private final int row;
    private final int col;
    private Disk disk;
    private final Field[] neighbors;

    public BoardField(int col, int row) {
        this.row = row;
        this.col = col;
        this.disk = null;
        this.neighbors = new Field[9];
        for (int i = 0; i < 9; i++) {
            this.neighbors[i] = null;
        }
    }

    @Override
    public int hasDisk() {
        if (this.disk == null) {
            return 0;
        }
        return 1;
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
    public boolean put(Disk disk) {
        if (this.disk == null) {
            this.disk = disk;
            this.disk.setColAndRow(this.col, this.row, this);
            return true;
        }
        return false;
    }

    @Override
    public boolean equals(java.lang.Object obj) {
        if (obj instanceof BoardField) {
            BoardField tmp = (BoardField) obj;
            if (this.row == tmp.row && this.col == tmp.col){
                if (tmp.disk != null) {
                    return this.disk.equals(tmp);
                }
                else {
                    return (this.disk == null);
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
    public boolean remove(Disk disk) {
        if (this.disk.equals(disk)) {
            this.disk = null;
            return true;
        }
        return false;
    }

    @Override
    public Disk get() {
        return this.disk;
    }

    @Override
    public boolean isEmpty() {
        return this.disk == null;
    }



}