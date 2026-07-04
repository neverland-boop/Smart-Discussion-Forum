package view;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.CheckBox;
import javafx.stage.Stage;
import java.io.IOException;
import javafx.scene.control.ToggleButton;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;

import dto.LoginResponse;
import service.AuthService;
import storage.TokenStorage;

public class Reg {

    @FXML private CheckBox rulesCheckbox;

    @FXML private ToggleButton eyeToggle;
    @FXML private PasswordField passwordField;
    @FXML private TextField passwordTextField;
    @FXML private PasswordField confirmPasswordField;
    @FXML private TextField emailTextField;
    @FXML private TextField fullNameTextField;

    @FXML
    private void handleRegisterAction(ActionEvent event) {
        // 1. Rules gateway (recess brief requirement #5 / SDD onboard()).
        if (!rulesCheckbox.isSelected()) {
            loadScreen(event, "/resources/declined.fxml");
            return;
        }

        String fullName = fullNameTextField.getText();
        String email = emailTextField.getText();
        String password = passwordField.getText();
        String confirmPassword = confirmPasswordField.getText();

        if (fullName == null || fullName.isBlank() || email == null || email.isBlank() || password == null || password.isBlank()) {
            showMessage("Please fill in all fields.");
            return;
        }

        if (!password.equals(confirmPassword)) {
            showMessage("Passwords do not match!");
            return;
        }

        // 2. Send to Duncan's Sanctum-backed /api/register endpoint.
        //    (Field names match Laravel/Breeze validation: password_confirmation, not confirmPassword.)
        LoginResponse result = AuthService.register(fullName, email, password, confirmPassword);

        if (result.success) {
            // Sanctum returns a real bearer token here — we store THAT,
            // never a locally generated UUID.
            if (result.token != null) {
                TokenStorage.saveToken(result.token);
                TokenStorage.saveLoggedInEmail(email);
            }

            showMessage("Account registered successfully. Welcome to the Smart Discussion Forum.");
            goToLogin(event);

        } else {
            showMessage(result.message != null ? result.message : "Registration failed.");
        }
    }

    @FXML
    private void handleReturnToLogin(ActionEvent event) {
        goToLogin(event);
    }

    private void goToLogin(ActionEvent event) {
        try {
            Stage currentStage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/resources/view/login.fxml"));
            Parent root = loader.load();
            currentStage.setScene(new Scene(root));
            currentStage.show();
        } catch (Exception ex) {
            ex.printStackTrace();
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
            passwordTextField.setText(passwordField.getText());
            passwordTextField.setVisible(true);
            passwordTextField.setManaged(true);

            passwordField.setVisible(false);
            passwordField.setManaged(false);
        } else {
            passwordField.setText(passwordTextField.getText());
            passwordField.setVisible(true);
            passwordField.setManaged(true);

            passwordTextField.setVisible(false);
            passwordTextField.setManaged(false);
        }
    }

    public void handleLogout() {
        TokenStorage.clearToken();
        System.out.println("Local authentication tokens cleared successfully.");
    }

    private void showMessage(String message) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}
