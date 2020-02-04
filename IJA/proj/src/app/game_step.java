package app;

public class game_step{
    private String figure_type;
    private int from_x;
    private int from_y;
    private int to_x;
    private int to_y;
    private boolean check_mate;
    private boolean mate;

    game_step() {
        check_mate = false;
        mate = false;
    }

    public boolean isCheck_mate() {
        return check_mate;
    }

    public void setCheck_mate(boolean check_mate) {
        this.check_mate = check_mate;
    }

    public boolean isMate() {
        return mate;
    }

    public void setMate(boolean mate) {
        this.mate = mate;
    }

    public String getFigure_type() {
        return figure_type;
    }

    public void setFigure_type(String figure_type) {
        this.figure_type = figure_type;
    }

    public int getFrom_x() {
        return from_x;
    }

    public void setFrom_x(int from_x) {
        this.from_x = from_x;
    }

    public int getFrom_y() {
        return from_y;
    }

    public void setFrom_y(int from_y) {
        this.from_y = from_y;
    }

    public int getTo_x() {
        return to_x;
    }

    public void setTo_x(int to_x) {
        this.to_x = to_x;
    }

    public int getTo_y() {
        return to_y;
    }

    public void setTo_y(int to_y) {
        this.to_y = to_y;
    }

    public void print_step(){
        System.out.println(
                "STEP OF " + figure_type + "\n" +
                "FROM: " + from_x + "," + from_y + "\n" +
                        "TO: " + to_x + "," + to_y
        );
    }
}
