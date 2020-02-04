package app;

public enum move_list {
    UP (0,1),
    UP_RIGHT(1, 1),
    RIGHT(1, 0),
    DOWN_RIGHT(1, -1),
    DOWN(0,-1),
    DOWN_LEFT(-1, -1),
    LEFT(-1, 0),
    UP_LEFT(-1, 1),

    KNIGHT_RIGHT_DOWN(2, -1),
    KNIGHT_DOWN_RIGHT(1, -2),
    KNIGHT_DOWN_LEFT(-1, -2),
    KNIGHT_LEFT_DOWN(-2, -1),
    KNIGHT_LEFT_UP(-2, 1),
    KNIGHT_UP_LEFT(-1, 2),
    KNIGHT_UP_RIGHT(1, 2),
    KNIGHT_RIGHT_UP(2, 1),

    DOUBLE_UP(0, 2),
    DOUBLE_DOWN(0, -2);

    private int x;
    private int y;

    private void setX(int x){
        this.x = x;
    }

    public int getX(){
        return this.x;
    }

    private void setY(int y){
        this.y = y;
    }

    public int getY(){
        return this.y;
    }

    public boolean isEqual(move_list m) {
        if (this.x == m.getX()) {
            if (this.y == m.getY()){
                return true;
            }
        }
        return false;
    }

    public boolean isEqual(int x, int y) {
        if (this.x == x) {
            if (this.y == y) {
                return true;
            }
        }
        return false;
    }

    move_list(int x, int y) {
        this.x = x;
        this.y = y;
    }
}
