package parser;

import java.util.ArrayList;
import java.util.regex.PatternSyntaxException;

public class parser {
    private String input;
    private String[] current_move;
    private int current_step;
    private int move_count;
    private ArrayList<String[]> moves_array = new ArrayList<>();

    public int getMove_count() {
        return move_count;
    }

    public void setMove_count(int move_count) {
        this.move_count = move_count;
    }

    public parser (){
        current_step = 0;
    }

    public void setCurrent_step(int current_step) {
        this.current_step = current_step;
    }

    public int getCurrent_step() {
        return current_step;
    }

    public String getInput() {
        return input;
    }

    public void setInput(String input) {
        this.input = input;
    }

    public String[] getCurrent_move() {
        return current_move;
    }

    public void setCurrent_move(String[] current_move) {
        this.current_move = current_move;
    }

    public ArrayList<String[]> getMoves_array() {
        return moves_array;
    }

    public void setMoves_array(ArrayList<String[]> moves_array) {
        this.moves_array = moves_array;
    }



    public void parse_line(String line) {
        String[] line_content;
        try {
            line_content = line.split("\\s+");
            current_move[0] = line;
            try{
                current_move[1] = line_content[1];
                current_move[2] = line_content[2];
            }
            catch (NullPointerException e) {
                System.out.println("NullPointer");
            }
            catch (ArrayIndexOutOfBoundsException e){
                System.out.println("IndexOutOfBounds");
            }
        } catch (PatternSyntaxException exception) {
            System.out.println("Cannot split string.");
        }
    }

    public void parse_input(String file_input){
        setInput(file_input);
        String[] lines = getInput().split("\\r?\\n");
        moves_array.clear();
        for (String line: lines){
            current_move = new String[3];
            parse_line(line);
            moves_array.add(current_move);
        }

        move_count = moves_array.size();
    }
}
