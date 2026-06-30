package view;

import java.util.UUID;

import javafx.application.Application;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Hyperlink;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

import storage.TokenStorage;

public class LoginScreen extends Application {

    // Counts how many times the user entered wrong details
    private int failedAttempts = 0;

    @Override
    public void start(Stage stage) {

        // Check if the user already has a saved token
        String savedToken = TokenStorage.getToken();

        if (savedToken != null) {
            showMessage("Welcome back!\nYou were remembered.");
        }


        // CREATING CONTROLS

        Label title = new Label("Smart Discussion Forum");
                     title.setStyle("-fx-font-size: 24px; -fx-font-weight: bold;");

              Label subtitle = new Label("Please sign in to continue.");

              TextField emailField = new TextField();
         emailField.setPromptText("Enter your email");

               PasswordField passwordField = new PasswordField();
          passwordField.setPromptText("Enter your password");

           CheckBox rememberMe = new CheckBox("Remember me");

               Hyperlink forgotPassword = new Hyperlink("Forgot Password?");

           Button loginButton = new Button("Login");
        loginButton.setPrefWidth(200);


        // LOGIN BUTTON ACTION

        loginButton.setOnAction(e -> {

            // Stop user after 3 failed attempts
         if (failedAttempts >= 3) {
                showMessage("Too many failed attempts!");
                loginButton.setDisable(true);
                return;
           }

            // Get what the user typed
            String email = emailField.getText().trim();
          String password = passwordField.getText();

            // Check if email is empty
            if (email.isEmpty()) {
                showMessage("Please enter your email.");
                return;
            }

            // Check if email looks correct
               if (!email.contains("@")) {
                showMessage("Invalid email address.");
                return;
            }

            // Check if password is empty
            if (password.isEmpty()) {
                showMessage("Please enter your password.");
                return;
            }

            // Temporary login details
            String correctEmail = "admin@gmail.com";
            String correctPassword = "123456";

            // Check if login details are correct
            if (!email.equals(correctEmail)
                    || !password.equals(correctPassword)) {

                failedAttempts++;

                showMessage(
                        "Wrong email or password.\n" +
                        "Attempts: " + failedAttempts + "/3"
                );

                return;
            }

            // Reset attempts after successful login
            failedAttempts = 0;

            // Create a random token
            String token = UUID.randomUUID().toString();

            // Save token if Remember Me is selected
            if (rememberMe.isSelected()) {
                TokenStorage.saveToken(token);
            }

            showMessage(
                    "Login Successful!\n\n" +
                    "Your token is:\n" + token
            );
        });


        // LAYOUT

        VBox root = new VBox(15);

        root.setPadding(new Insets(30));
        root.setAlignment(Pos.CENTER);

        root.getChildren().addAll(
                title,
                subtitle,
         emailField,
                passwordField,
                rememberMe,
          forgotPassword,
                loginButton
        );


        // SCENE


        Scene scene = new Scene(root, 450, 400);

        stage.setTitle("Login Screen");
        stage.setScene(scene);
        stage.show();
    }

    // Method for showing popup messages
    private void showMessage(String message) {

        Alert alert = new Alert(Alert.AlertType.INFORMATION);

        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }

    // Main method - starts the program
    public static void main(String[] args) {
        launch(args);
    }
}
