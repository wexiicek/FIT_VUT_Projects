package ija.ija2018.homework2.common;

import ija.ija2018.homework2.GameFactory;
import ija.ija2018.homework2.game.Board;

import java.util.ArrayList;

public class Game_imp extends GameFactory implements Game{

    private ArrayList<Figure> moves = new ArrayList<>();
    private boolean isChess = false;
    private Board gameBoard;

    public Game_imp(Board board, boolean isChess){
        this.gameBoard = board;
        this.isChess = isChess;
    }

    public boolean placeFigure(Figure fig, Field field) {
        Figure tempFrom = null;
        Figure tempTo = null;
        if (this.isChess) {
            if (fig != null) {
                tempFrom = new Game_Figure(fig);
            }
            if (field.get() != null) {
                tempTo = new Game_Figure(field.get());
            }
            this.moves.add(tempFrom);
            this.moves.add(tempTo);

            Field temp = fig.getMyField();
            fig.setColAndRow(field.getCol(), field.getRow(), field);

            if (fig.isRook()) {
                field.rmFig();
            }
            field.put(fig);
            temp.remove(fig);
            return true;
        }
        else {
            if (fig != null) {
                tempFrom = new Game_Figure(fig);
            }

            Field temp = fig.getMyField();
            fig.setColAndRow(field.getCol(), field.getRow(), field);
            tempTo = fig;
            this.moves.add(tempFrom);
            this.moves.add(tempTo);
            field.put(fig);
            temp.rmFig();
            return true;
        }
    }


   public boolean move(Figure figure, Field field){
        if (this.isChess) {
            //Figure is pawn
            if (!figure.isRook()) {
                if (field.isEmpty()) {
                    if (figure.isWhite()) {
                        if (figure.getMyField().getCol() == field.getCol() && figure.getMyField().getRow() + 1 == field.getRow()) {
                            if (placeFigure(figure, field)) {
                                return true;
                            }
                        }
                    } else {
                        if (figure.getMyField().getCol() == field.getCol() && figure.getMyField().getRow() - 1 == field.getRow()) {
                            if (placeFigure(figure, field)) {
                                return true;
                            }
                        }
                    }
                }
            }

            //Figure is rook
            else {
                //Moving in the Y axis
                if (field.getCol() == figure.getMyField().getCol()) {
                    //Moving up
                    if (field.getRow() > figure.getMyField().getRow()) {
                        Field temp = figure.getMyField().nextField(Field.Direction.U);
                        while (!temp.equals(field)) {
                            if (!temp.isEmpty()) {
                                if (!(temp.getRow() == field.getRow())) {
                                    return false;
                                } else {
                                    placeFigure(figure, field);
                                    return true;
                                }
                            }
                            temp = temp.nextField(Field.Direction.U);
                        }
                        placeFigure(figure, field);
                        return true;
                    }
                    //Moving down
                    else {
                        Field temp = figure.getMyField().nextField(Field.Direction.D);
                        while (!temp.equals(field)) {
                            if (!temp.isEmpty()) {
                                if (!(temp.getRow() == field.getRow())) {
                                    return false;
                                } else {
                                    placeFigure(figure, field);
                                    return true;
                                }
                            }
                            temp = temp.nextField(Field.Direction.D);
                        }
                        placeFigure(figure, field);
                        return true;
                    }
                }

                //Moving in the X axis
                else if (field.getRow() == figure.getMyField().getRow()) {
                    //Moving right
                    if (field.getCol() > figure.getMyField().getCol()) {
                        Field temp = figure.getMyField().nextField(Field.Direction.R);
                        while (!temp.equals(field)) {
                            if (!temp.isEmpty()) {
                                if (!(temp.getCol() == field.getCol())) {
                                    return false;
                                } else {
                                    placeFigure(figure, field);
                                    return true;
                                }
                            }
                            temp = temp.nextField(Field.Direction.R);
                        }
                        placeFigure(figure, field);
                        return true;
                    }

                    //Moving left
                    else {
                        Field temp = figure.getMyField().nextField(Field.Direction.L);
                        while (!temp.equals(field)) {
                            if (!temp.isEmpty()) {
                                if (!(temp.getCol() == field.getCol())) {
                                    return false;
                                } else {
                                    placeFigure(figure, field);
                                    return true;
                                }
                            }
                            temp = temp.nextField(Field.Direction.L);
                        }
                        placeFigure(figure, field);
                        return true;
                    }
                }

            }
            return false;
        }
        else {
            if (field.isEmpty()) {
                if (figure.getMyField().getRow() + 1 == field.getRow()) {
                    if (figure.getMyField().getCol() + 1 == field.getCol() || figure.getMyField().getCol() - 1 == field.getCol()) {
                        placeFigure(figure, field);
                        return true;
                    }
                }
            }
            return false;
        }
    }

    public void undo(){
            Field to = null;
            Field from = this.gameBoard.getField(moves.get(moves.size() - 2).getCol(), moves.get(moves.size() - 2).getRow());
            if (moves.get(moves.size() - 1) != null) {
                to = this.gameBoard.getField(moves.get(moves.size() - 1).getCol(), moves.get(moves.size() - 1).getRow());
            }
            if (this.isChess) {
                from.put(moves.get(moves.size() - 2));
                if (to != null) {
                    to.rmFig();
                    to.put(moves.get(moves.size() - 1));
                }
            }
            else {
                from.put(moves.get(moves.size() - 1));
                from.get().setColAndRow(moves.get(moves.size()-2).getCol(), moves.get(moves.size()-2).getRow(), moves.get(moves.size()-2).getMyField());
                if (to != null) {
                    to.rmFig();
                }
            }
            moves.remove(moves.size() - 1);
            moves.remove(moves.size() - 1);
    }
}
