package app;

import javafx.application.Application;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.image.Image;
import javafx.stage.Stage;

public class Main extends Application
{
    public static void main(String[] args)
    {
        try
        {
            launch(args);
            System.exit(0);
        }
        catch (Exception error)
        {
            error.printStackTrace();
            System.exit(0);
        }
    }

    public static TabPane tabs = new TabPane();
    private int tab_num = 1;

    @Override
    public void start(Stage mainStage)
    {
        mainStage.setTitle("Kočess"); // Set stage title
        mainStage.getIcons().add(new Image("file:lib/main.png")); // Set stage icon
        mainStage.setMinHeight(780);
        mainStage.setMinWidth(820);

        add_new_game_tab();

        Scene mainScene = new Scene(tabs);

        mainStage.setScene(mainScene);

        mainScene.getStylesheets().add("assets/stylesheet.css"); // Setting scene css
        mainStage.show(); // Display stage
    }

    public void add_new_game_tab(){
        tab_ temp = new tab_();
        tabs.getTabs().add(temp.getTab());
    }

    public static TabPane getTabs() {
        return tabs;
    }

    public void setTabs(TabPane tabs) {
        tabs = tabs;
    }
}