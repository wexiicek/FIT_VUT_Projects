package app;

import javafx.application.Platform;

import java.util.concurrent.atomic.AtomicBoolean;

public class MyRunnable implements Runnable {
    private player p;
    private final AtomicBoolean running = new AtomicBoolean(false);
    private int delay;
    private chess_board board;


    public MyRunnable(player p, int d, chess_board b) {
        this.p = p;
        this.delay = d;
        this.board = b;
    }

    public void setBoard(chess_board b) {
        this.board = b;
    }

    public void set_delay(int d) {
        this.delay = d;
    }

    public void stop() {
        running.set(false);
    }

    public void start() {
        running.set(true);
    }

    public Boolean get_state() {
        return running.get();
    }

    public void run() {
        while (true) {
            if (Thread.currentThread().isInterrupted()){
                break;
            }
            if (running.get()) {
                try {
                    System.out.println("Threading.. " + delay);
                    Platform.runLater(new Runnable() {
                        @Override
                        public void run() {
                            if (p.initiate_player(board)) {
                                Thread.currentThread().interrupt();
                            }
                        }
                    });

                    Thread.sleep(delay);
                } catch (InterruptedException e) {
                    System.out.println("err");
                }
            }
            /*
            try {
                Thread.sleep(100);
            }
            catch (InterruptedException e ){
                System.out.println("err");
            }
            */
        }
    }
}