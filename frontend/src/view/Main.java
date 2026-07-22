package view;

import javafx.application.Application;
import javafx.scene.Scene;
import javafx.scene.layout.StackPane;
import javafx.stage.Stage;
import utils.DatabaseConnection;
import java.net.URL;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) {
        try {
            NavigationManager.init(primaryStage);
            DatabaseConnection.initializeDatabase();

            LoginScreen loginScreen = new LoginScreen();
            StackPane root = loginScreen.build(primaryStage);

            Scene scene = new Scene(root, 1000, 700);

            // Load style.css from src/main/resources/view/style.css
            try {
                URL cssUrl = getClass().getResource("/view/style.css");
                if (cssUrl != null) {
                    scene.getStylesheets().add(cssUrl.toExternalForm());
                } else {
                    System.err.println("CSS Warning: style.css not found in classpath resources");
                }
            } catch (Exception ex) {
                System.err.println("CSS Error: Failed to load style.css");
            }

            primaryStage.setTitle("Smart Discussion Forum");
            primaryStage.setScene(scene);
            primaryStage.setResizable(false);
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
