package app;

import javafx.application.Platform;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.EventHandler;
import javafx.scene.control.*;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.input.KeyCode;
import javafx.scene.input.KeyCodeCombination;
import javafx.scene.input.KeyCombination;
import javafx.scene.input.MouseEvent;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.HBox;
import javafx.stage.DirectoryChooser;
import javafx.stage.FileChooser;
import javafx.stage.Stage;
import parser.parser;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;
import java.util.Optional;
import java.util.Scanner;

public class tab_ extends Tab {
    private Tab tab;
    private File file;
    private File working_directory = new File(System.getProperty("user.dir"));
    private ListView list_view;
    private menu_bar menu;
    private chess_board board;
    private final FileChooser fileChooser;
    public final ObservableList data;
    private int current_step;
    private BorderPane game_view;
    private HBox controls;
    private BorderPane tab_view;
    private player player;
    private parser parser;
    private boolean is_short_format;
    private MyRunnable runnable;
    private Thread thread;
    private int delay;
    private boolean move_by_user;

    public tab_(){
        super();
        parser = new parser(); // Initialization of the parser
        player = new player();
        list_view = new ListView();
        current_step = 1;
        fileChooser = new FileChooser();
        data = FXCollections.observableArrayList();
        board = new chess_board(true, parser, list_view);
        menu = new menu_bar();
        generateMenuBar();
        game_view = new BorderPane(); // Border Pane for displaying the game area
        initialize_game_view();
        controls = new HBox();
        initialize_control_buttons();
        tab_view = new BorderPane();
        initialize_tab_view();
        tab = new Tab();
        tab.setContent(tab_view);
        tab.setText("Game Tab");
        is_short_format = false;
        player.setParser(parser);
        player.setBoard(board);
        delay = 500;
        runnable = new MyRunnable(player, delay, board);
        thread = new Thread(runnable);
        player.setMr(runnable);
        list_view.setOnMouseClicked(new EventHandler<MouseEvent>() {

            @Override
            public void handle(MouseEvent event) {
                System.out.println("clicked on " + list_view.getSelectionModel().getSelectedItem());
                game_jump(list_view.getSelectionModel().getSelectedItem());
            }
        });
    }

    public void game_jump(Object obj) {
        String s = String.valueOf(obj);
        chess_board temp = new chess_board(true, parser, list_view);
        //System.out.println("Here.. "+s+parser.getMoves_array().get(parser.getCurrent_step())[0]);
        parser.setCurrent_step(0);
        while (!parser.getMoves_array().get(parser.getCurrent_step())[0].equals(s)){
            player.initiate_player(temp);
        }
        player.initiate_player(temp);
        parser.setCurrent_step(player.char_to_num(s.charAt(0)));
        board = temp;
        runnable.setBoard(board);
        initialize_game_view();
    }

    public Tab getTab() {
        return tab;
    }

    private void initialize_tab_view(){
        tab_view.setTop(menu.getMenu_bar());
        tab_view.setLeft(game_view);
        tab_view.setRight(list_view);
        tab_view.setBottom(controls);
    }

    private void step_back() {
        if (parser.getCurrent_step() >= 0){
            try {

                if (parser.getCurrent_step() == 1) {
                    change_board();
                } else {
                    parser.setCurrent_step(parser.getCurrent_step() - 1);
                    game_jump(list_view.getItems().get(parser.getCurrent_step() - 1));

                    //if (board.isPlayed_by_player()) {
                      //  list_view.getItems().remove(parser.getCurrent_step());
                        //initialize_game_view();
                    //}
                }
            }
            catch (ArrayIndexOutOfBoundsException e) {
                System.out.println(e);
            }
            catch (NullPointerException e) {
                System.out.println(e);
            }
            catch (IndexOutOfBoundsException e) {
                System.out.println(e);
            }
        }
    }

    private void initialize_control_buttons(){
        Button prev_step = new Button("<-"); // Button left
        Button next_step = new Button("->"); // Button play
        ToggleButton play_but = new ToggleButton("Play"); // Button right
        prev_step.getStyleClass().add("buttn"); // Setting button css
        next_step.getStyleClass().add("buttn"); // Setting button css
        play_but.getStyleClass().add("buttn"); // Setting button css
        next_step.setOnAction(e -> player.initiate_player(board));
        prev_step.setOnAction(e -> step_back());
        play_but.setOnAction(e -> handle_thread());
        controls.getChildren().add(prev_step);
        controls.getChildren().add(play_but);
        controls.getChildren().add(next_step);
    }

    private void handle_thread() {
        if (runnable.get_state()) {
            runnable.stop();
        }
        else {
            runnable.start();
        }
    }

    private void initialize_game_view(){
        Image left_row = new Image("file:lib/left_row.png"); // Left tile descriptors (image)
        Image top_row = new Image("file:lib/top_row.png"); // Top tile descriptors (image)
        ImageView left = new ImageView(); // Image view for displaying descriptors
        ImageView top = new ImageView(); // Image view for displaying descriptors
        left.setImage(left_row);
        top.setImage(top_row);
        game_view.setTop(top);
        game_view.setLeft(left);
        game_view.setCenter(board);
    }

    private void set_delay_interval() {
        TextInputDialog ask_delay = new TextInputDialog(String.valueOf(delay));
        ask_delay.setTitle("Playback delay value");
        ask_delay.setHeaderText("Please, enter playback delay below.");
        ask_delay.setContentText("Playback delay (ms):");

        Optional<String> result = ask_delay.showAndWait();
        if (result.isPresent()){
            this.delay = Integer.parseInt(result.get());
            runnable.set_delay(delay);
        }
    }

    private void change_board(){
        board = new chess_board(true, parser, list_view);
        parser.setCurrent_step(0);
        list_view.getSelectionModel().select(0);
        initialize_game_view();
    }

    private void generateMenuBar()
    {
        MenuItem delay_dialog = menu.getSet_delay();
        delay_dialog.setOnAction(e->set_delay_interval());
        delay_dialog.setAccelerator(new KeyCodeCombination(KeyCode.D, KeyCombination.CONTROL_DOWN));

        MenuItem help_choice = menu.getMenu_help();
        help_choice.setAccelerator( new KeyCodeCombination(KeyCode.F1) );
        help_choice.setOnAction(e -> show_guide());

        MenuItem reset_game = menu.getReset_game();
        reset_game.setAccelerator(new KeyCodeCombination(KeyCode.R, KeyCombination.CONTROL_DOWN));
        reset_game.setOnAction(e -> change_board());

        MenuItem file_opener = menu.getMenu_open_file();
        file_opener.setOnAction(e -> open_source_file());
        file_opener.setAccelerator(new KeyCodeCombination(KeyCode.F, KeyCombination.CONTROL_DOWN));

        MenuItem add_tab = menu.getMenu_add_tab();
        add_tab.setOnAction(e -> Main.getTabs().getTabs().add(new tab_().getTab()));
        add_tab.setAccelerator(new KeyCodeCombination(KeyCode.N, KeyCombination.CONTROL_DOWN));

        MenuItem save_file = menu.getSave_game_to_file();
        save_file.setOnAction(e -> save_to_file());
        save_file.setAccelerator(new KeyCodeCombination(KeyCode.S, KeyCombination.CONTROL_DOWN));

        MenuItem quit_app = menu.getMenu_quit();
        quit_app.setOnAction(e -> close_app());
        quit_app.setAccelerator( new KeyCodeCombination(KeyCode.Q, KeyCombination.CONTROL_DOWN) );
    }

    private void rm_from_lv(){
        list_view.getItems().removeAll();
        list_view.getItems().clear();
        list_view.refresh();
    }

    private void save_to_file() {
        if (parser.getInput().contains("null")){
            String tmp = parser.getInput();
            String tmp_res = tmp.substring(0, tmp.indexOf('n'));
            tmp_res = tmp_res + tmp.substring(tmp.indexOf('n')+4, tmp.length());
            parser.setInput(tmp_res);
        }
        FileChooser dc = new FileChooser();
        dc.setTitle("Choose a file");
        dc.setInitialDirectory(working_directory);

        dc.getExtensionFilters().addAll(
                new FileChooser.ExtensionFilter("Text Files", "*.txt"),
                new FileChooser.ExtensionFilter("All Files", "*.*")
        );

        File output = dc.showSaveDialog(new Stage());

        System.out.println(output.getPath());
        try {
            PrintWriter pw = new PrintWriter(output, "UTF-8");
            pw.println(parser.getInput());
            pw.close();
        }
        catch (FileNotFoundException e) {
            System.out.println("File not found");
        }
        catch (UnsupportedEncodingException f) {
            System.out.println("Unsupported encoding");
        }
    }

    private void close_app(){
        Platform.exit();
        System.exit(0);
    }

    private void show_guide()
    {
        Alert infoAlert = new Alert(Alert.AlertType.INFORMATION);
        infoAlert.setTitle("Kočess Guide");
        infoAlert.setHeaderText("Kočess Guide");
        infoAlert.setContentText("" +
                "This software allows you to play a game of chess.\n" +
                "You can either play manually or load a game transcript from a file.\n" +
                "To open a file, open the Game menu and choose Open a file.. (or CTRL+F)\n" +
                "To create a new game tam, open the Game menu and choose Create new tab(or CTRL+N)\n"+
                "To play manually, simply click on the figure you want to move" +
                " followed by a click on the desired field.\n");
        infoAlert.showAndWait();
    }

    private void open_source_file() {
        thread.start();
        fileChooser.setInitialDirectory(working_directory);
        file = fileChooser.showOpenDialog(new Stage());
        parser.getMoves_array().clear();
        list_view.getItems().clear();
        if (file != null) {

            String content = "";

            try {
                content = new Scanner(file).useDelimiter("\\Z").next();
            }
            catch (FileNotFoundException er) {
                System.out.println("ahoj");
            }
            parser.parse_input(content);
            for (String[] item: parser.getMoves_array()){
                data.add(item[0]);
                for (String item_: item) {
                    System.out.print(item_ + " | ");
                }
                list_view.getItems().add(item[0]);
                System.out.println();
            }
            is_short_format = ask_for_format();
        }

        player.setIs_short_format(is_short_format);
        list_view.getSelectionModel().select(0);
    }

    private boolean ask_for_format(){
        Alert alert = new Alert(Alert.AlertType.CONFIRMATION);
        alert.getButtonTypes().set(1, new ButtonType("No", ButtonBar.ButtonData.CANCEL_CLOSE));
        alert.setHeaderText("Fill in information");
        alert.setContentText("Is the game written in shortened format?");
        Optional<ButtonType> result = alert.showAndWait();
        if (result.isPresent() &&result.get() == ButtonType.OK){
            return true;
        }
        return false;
    }

    /*public tab_ create_game_tab(int num){


        /*next_step.setOnAction(new EventHandler<ActionEvent>() {
            @Override
            public void handle(ActionEvent event) {
                // TODO
                current_step += 1;
                move mov = new move(6,1,6,2);
                tile space = board.getSpace(6,1);
                board.setActive_tile(space);
                board.move(mov);
                board.setNonActiveSpace();
                mov = new move(1,6,1,5);
                space = board.getSpace(1,6);
                board.setActive_tile(space);
                board.move(mov);
                board.setNonActiveSpace();
            }
        });

    }*/

    public ObservableList getData() {
        return data;
    }

    public chess_board getBoard() {
        return this.board;
    }

    public parser getParser() {
        return parser;
    }
}
