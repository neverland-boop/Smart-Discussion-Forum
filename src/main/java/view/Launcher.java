package view;

import javafx.application.Application;
import javafx.stage.Stage;

public class Launcher extends Application {

    @Override
    public void start(Stage stage)
            throws Exception {

        new LoginScreen().start(stage);
    }

    public static void main(String[] args) {
        launch(args);
    }
}