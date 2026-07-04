package view;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import utils.DatabaseConnection;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) {
        try {
            // Bidal's Sprint 1 deliverable: make sure the local SQLite
            // structure exists before anything else touches it.
            DatabaseConnection.initializeDatabase();

            // Patience/Anthony's login screen — same FXML + style.css
            // approach as registration/decline, for UI consistency.
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/resources/view/login.fxml"));
            Parent root = loader.load();

            primaryStage.setTitle("Smart Discussion Forum");
            primaryStage.setScene(new Scene(root, 1000, 700));
            primaryStage.show();

        } catch (Exception e) {
            System.err.println("Error launching the login screen:");
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}
