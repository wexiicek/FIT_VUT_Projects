package app;

import javafx.scene.control.Menu;
import javafx.scene.control.MenuBar;
import javafx.scene.control.MenuItem;

public class menu_bar extends MenuBar {
    private MenuBar menu_bar;
    private Menu game_menu;

    private MenuItem menu_quit;
    private MenuItem menu_open_file;
    private MenuItem menu_add_tab;
    private MenuItem save_game_to_file;
    private MenuItem set_delay;
    private MenuItem reset_game;

    private Menu help_menu;
    private MenuItem menu_help;

    public menu_bar() {

        menu_bar = new MenuBar();

        game_menu = new Menu("Game");
        help_menu = new Menu("Help");
        menu_bar.getMenus().add(game_menu);
        menu_bar.getMenus().add(help_menu);

        menu_add_tab = new MenuItem("Create new tab");
        set_delay = new MenuItem("Set playback delay");
        menu_open_file = new MenuItem("Open a file..");
        save_game_to_file = new MenuItem("Save game to file..");
        menu_quit = new MenuItem("Quit");
        reset_game = new MenuItem("Reset game");

        game_menu.getItems().add(menu_add_tab);
        game_menu.getItems().add(reset_game);
        game_menu.getItems().add(set_delay);
        game_menu.getItems().add(menu_open_file);
        game_menu.getItems().add(save_game_to_file);
        game_menu.getItems().add(menu_quit);


        menu_help = new MenuItem("Help");
        help_menu.getItems().add(menu_help);

    }

    public MenuItem getSet_delay(){return set_delay;}

    public MenuItem getSave_game_to_file() {
        return save_game_to_file;
    }

    public void setSave_game_to_file(MenuItem save_game_to_file) {
        this.save_game_to_file = save_game_to_file;
    }

    public MenuBar getMenu_bar() {
        return menu_bar;
    }

    public void setMenu_bar(MenuBar menu_bar) {
        this.menu_bar = menu_bar;
    }

    public Menu getGame_menu() {
        return game_menu;
    }

    public void setGame_menu(Menu game_menu) {
        this.game_menu = game_menu;
    }

    public MenuItem getMenu_quit() {
        return menu_quit;
    }

    public void setMenu_quit(MenuItem menu_quit) {
        this.menu_quit = menu_quit;
    }

    public MenuItem getMenu_open_file() {
        return menu_open_file;
    }

    public void setMenu_open_file(MenuItem menu_open_file) {
        this.menu_open_file = menu_open_file;
    }

    public MenuItem getMenu_add_tab() {
        return menu_add_tab;
    }

    public void setMenu_add_tab(MenuItem menu_add_tab) {
        this.menu_add_tab = menu_add_tab;
    }

    public Menu getHelp_menu() {
        return help_menu;
    }

    public void setHelp_menu(Menu help_menu) {
        this.help_menu = help_menu;
    }

    public MenuItem getMenu_help() {
        return menu_help;
    }

    public void setMenu_help(MenuItem menu_help) {
        this.menu_help = menu_help;
    }

    public MenuItem getReset_game() {
        return reset_game;
    }

    public void setReset_game(MenuItem reset_game) {
        this.reset_game = reset_game;
    }
}
