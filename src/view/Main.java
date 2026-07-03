package view;
import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class Main extends Application {

        @Override
        public void start(Stage primaryStage) throws Exception {

            java.util.prefs.Preferences.userNodeForPackage(Reg.class).remove("auth_token");
            System.out.println("Temporary token wipe executed successfully!");

            Parent root = FXMLLoader.load(getClass().getResource("/registration.fxml"));
            primaryStage.setTitle("Smart Discussion Forum");
            primaryStage.setScene(new Scene(root));
            primaryStage.show();
        }

        public static void main(String[] args) {

            launch(args);
        }
    }

