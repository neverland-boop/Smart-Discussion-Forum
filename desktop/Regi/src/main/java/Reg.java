import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.CheckBox;
import javafx.stage.Stage;
import java.io.IOException;

public class Reg {

    @FXML
    private CheckBox rulesCheckbox;

    /**
     * Triggered when the green 'Register' button is clicked.
     */
    @FXML
    private void handleRegisterAction(ActionEvent event) {
        // If the user ticked the box, let them through!
        if (rulesCheckbox.isSelected()) {
            System.out.println("Success! . Account created.");
            // Optional: loadScreen(event, "/resources/dashboard.fxml");
        }
        // If they DID NOT tick the box, they are automatically declined!
        else {
            loadScreen(event, "/declined.fxml");
        }
    }


    /**
     * Triggered when clicking 'Return to LogIn' from your second screen.
     */
    @FXML
    private void handleReturnToLogin(ActionEvent event) {
        loadScreen(event, "/registration.fxml"); // Routes back to the signup sheet
    }
    @FXML
    public void handleReturnToRegister(ActionEvent event) {
        try {
            // 1. Load the registration layout file
            Parent registerRoot = FXMLLoader.load(getClass().getResource("/registration.fxml"));

            // 2. Get the current active window (Stage)
            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();

            // 3. Swap the scene back to the registration view
            stage.setScene(new Scene(registerRoot));
            stage.show();

        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Could not load registration.fxml. Check the file name!");
        }
    }

    /**
     * Helper method to seamlessly flip between your FXML screens
     */
    private void loadScreen(ActionEvent event, String fxmlPath) {
        try {
            Parent root = FXMLLoader.load(getClass().getResource(fxmlPath));
            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            Scene scene = new Scene(root);
            stage.setScene(scene);
            stage.show();

        } catch (IOException e) {
            System.err.println("Error: Layout file missing at " + fxmlPath);
            e.printStackTrace();
        }
    }
}