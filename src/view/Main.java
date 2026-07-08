package view;
import javafx.application.Application;
import javafx.stage.Stage;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) {
        try {
            Connect.initializeDatabase();
            // 1. Create an instance of your colleague's LoginScreen class
            LoginScreen login = new LoginScreen();

            // 2. Run his custom method to launch the login layout first
           login.showLoginScreen(primaryStage);

        } catch (Exception e) {
            System.err.println("Error launching the login screen:");
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}


