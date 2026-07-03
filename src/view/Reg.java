package view;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.CheckBox;
import javafx.stage.Stage;
import java.io.IOException;
import javafx.scene.control.ToggleButton;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;


public class Reg {

    @FXML private CheckBox rulesCheckbox;

    @FXML private ToggleButton eyeToggle;
    @FXML private PasswordField passwordField;
    @FXML private TextField passwordTextField;
    @FXML private PasswordField confirmPasswordField;
    @FXML private TextField emailTextField;
    @FXML private TextField fullNameTextField;


    @FXML private void handleRegisterAction(ActionEvent event) {
        String fullName = fullNameTextField.getText(); // Make sure this fx:id exists!
        String email = emailTextField.getText();
        String password = passwordField.getText();
        String confirmPassword = confirmPasswordField.getText();

        if (!password.equals(confirmPassword)) {
            System.out.println("Error: Passwords do not match!");
            return;
        }

        // 3. Send the data to your backend class
        boolean isSuccess = BackendSe.saveUserToDatabase(fullName, email, password, confirmPassword);

        if (isSuccess) {
            System.out.println("Success! Account created in the database.");

            // token generation logic
            String userToken = java.util.UUID.randomUUID().toString();
            java.util.prefs.Preferences prefs = java.util.prefs.Preferences.userNodeForPackage(Reg.class);
            prefs.put("auth_token", userToken);
            prefs.put("logged_in_user", email);

        } else {
            System.out.println("Registration failed");
            loadScreen(event, "/declined.fxml");
        }
    }

    @FXML private void handleReturnToLogin(ActionEvent event) {
        loadScreen(event, "/registration.fxml");
    }
    @FXML public void handleReturnToRegister(ActionEvent event) {
        try {

            Parent registerRoot = FXMLLoader.load(getClass().getResource("/registration.fxml"));


            Stage stage = (Stage) ((Node) event.getSource()).getScene().getWindow();

            //  Swap the scene back to the registration view
            stage.setScene(new Scene(registerRoot));
            stage.show();

        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Could not load registration.fxml. Check the file name!");
        }
    }

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

    @FXML
    public void togglePasswordVisibility(ActionEvent event) {
        if (eyeToggle.isSelected()) {
            // Copy masked characters to the plain text field and show it
            passwordTextField.setText(passwordField.getText());
            passwordTextField.setVisible(true);
            passwordTextField.setManaged(true);

            passwordField.setVisible(false);
            passwordField.setManaged(false);
        } else {
            // Copy plain text back to the masked field and show it
            passwordField.setText(passwordTextField.getText());
            passwordField.setVisible(true);
            passwordField.setManaged(true);

            passwordTextField.setVisible(false);
            passwordTextField.setManaged(false);
        }
    }

    public void handleLogout() {
        // Access the storage node
        java.util.prefs.Preferences prefs = java.util.prefs.Preferences.userNodeForPackage(Reg.class);

        // Clear out the saved token data
        prefs.remove("auth_token");
        prefs.remove("logged_in_user");

        System.out.println("Local authentication tokens cleared successfully.");
    }
}