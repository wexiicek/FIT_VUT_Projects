package app;

import java.io.Serializable;

public class move implements Serializable {
    int oldX;
    int newX;
    int oldY;
    int newY;

    public move() {
        oldX = 0;
        oldY = 0;
        newX = 1;
        newY = 1;
    }

    public move(int oldX, int oldY, int newX, int newY) {
        this.oldX = oldX;
        this.oldY = oldY;
        this.newX = newX;
        this.newY = newY;
    }

    public String toString()
    {
        return (getCharLabel(oldX+1) + (oldY+1) + " to " + getCharLabel(newX+1) + (newY+1));
    }

    public int get_old_x(){
        return this.oldX;
    }

    public int get_old_y(){
        return this.oldY;
    }

    public int get_new_x(){
        return this.newX;
    }

    public int get_new_y(){
        return this.newY;
    }

    public void set_old_x(int oldX){
        this.oldX = oldX;
    }

    public void set_old_y(int oldY){
        this.oldY = oldY;
    }

    public void set_new_x(int newX){
        this.newX = newX;
    }

    public void set_new_y(int newX){
        this.newY = newX;
    }

    public int x_gap(){
        return this.newX - this.oldX;
    }

    public int y_gap(){
        return this.newY - this.oldY;
    }

    // Converts x number poisition to character label
    private String getCharLabel(int i) {
        if (i > 0 && i < 27) {
            return String.valueOf((char)(i + 64));
        }
        return null;
    }

}
