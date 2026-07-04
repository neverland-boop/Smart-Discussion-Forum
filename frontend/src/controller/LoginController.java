package controller;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.CheckBox;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

import dto.LoginResponse;
import service.AuthService;
import storage.TokenStorage;

/**
 * FXML controller for login.fxml.
 *
 * Sprint 1 (Patience & Anthony — "Build Java Login/Registration screens
 * and local token storage"): this screen no longer checks a hardcoded
 * email/password pair. It calls AuthService.login(), which hits Duncan's
 * Sanctum-backed /api/login endpoint, and persists the REAL token Sanctum
 * returns via TokenStorage — never a token generated on the client.
 */
public class LoginController {

    @FXML private TextField emailField;
    @FXML private PasswordField passwordField;
    @FXML private CheckBox rememberMe;

    private int failedAttempts = 0;

    @FXML
    public void initialize() {
        String token = TokenStorage.getToken();
        String email = TokenStorage.getLoggedInEmail();

        if (token != null && email != null) {
            showMessage("Welcome back, " + email + "!\nYou were remembered.");
        }
    }

    @FXML
    private void handleLoginAction(ActionEvent event) {

        if (failedAttempts >= 3) {
            showMessage("Too many failed attempts! Please try again later.");
            return;
        }

        String email = emailField.getText();
        String password = passwordField.getText();

        if (email == null || email.isBlank()) {
            showMessage("Please enter your email.");
            return;
        }

        if (!email.contains("@")) {
            showMessage("Invalid email.");
            return;
        }

        if (password == null || password.isBlank()) {
            showMessage("Please enter your password.");
            return;
        }

        LoginResponse result = AuthService.login(email, password);

        if (!result.success) {
            failedAttempts++;
            showMessage((result.message != null ? result.message : "Wrong credentials.")
                    + "\nAttempts: " + failedAttempts + "/3");
            return;
        }

        failedAttempts = 0;

        if (rememberMe.isSelected() && result.token != null) {
            TokenStorage.saveToken(result.token);
            TokenStorage.saveLoggedInEmail(email);
        }

        showMessage("Login Successful!");
        // TODO (later sprint): navigate to the real dashboard once it exists.
    }

    @FXML
    private void handleGoToRegister(ActionEvent event) {
        try {
            Stage currentStage = (Stage) ((Node) event.getSource()).getScene().getWindow();
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/resources/view/registration.fxml"));
            Parent root = loader.load();
            currentStage.setScene(new Scene(root));
            currentStage.show();
        } catch (Exception ex) {
            ex.printStackTrace();
        }
    }

    private void showMessage(String message) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}
