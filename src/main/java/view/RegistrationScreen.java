package view;

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.VBox;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.stage.Stage;
import main.java.dto.UserDAO;
import main.java.model.User;
import storage.TokenStorage;

public class RegistrationScreen {

    public void start(Stage stage) {

        Label title = new Label("Create Account");
        title.setFont(
                Font.font("Arial",
                        FontWeight.BOLD,
                        28)
        );

        TextField nameField =
                new TextField();
        nameField.setPromptText(
                "Full Name"
        );
        nameField.setMaxWidth(300);

        TextField emailField =
                new TextField();
        emailField.setPromptText(
                "Email Address"
        );
        emailField.setMaxWidth(300);

        PasswordField passwordField =
                new PasswordField();
        passwordField.setPromptText(
                "Password"
        );
        passwordField.setMaxWidth(300);

        PasswordField confirmField =
                new PasswordField();
        confirmField.setPromptText(
                "Confirm Password"
        );
        confirmField.setMaxWidth(300);

        Button registerButton =
                new Button("Register");
        registerButton.setPrefWidth(300);

        Hyperlink loginLink =
                new Hyperlink(
                        "Already have an account? Login"
                );

        Label statusLabel =
                new Label();

        VBox root = new VBox(20);

        root.setAlignment(Pos.CENTER);
        root.setPadding(
                new Insets(40)
        );

        root.getChildren().addAll(
                title,
                nameField,
                emailField,
                passwordField,
                confirmField,
                registerButton,
                loginLink,
                statusLabel
        );

        // =========================
        // REGISTER BUTTON
        // =========================
        registerButton.setOnAction(e -> {

                //
                UserDAO dao = new UserDAO();

if (dao.emailExists(email)) {
    statusLabel.setText(
            "Email already exists."
    );
    return;
}

User user =
        new User(
                name,
                email,
                password
        );

if (dao.register(user)) {

    TokenStorage.saveToken(
            email
    );

    new DashboardForm().start(stage);
}

            String name =
                    nameField.getText().trim();

            String email =
                    emailField.getText().trim();

            String password =
                    passwordField.getText();

            String confirm =
                    confirmField.getText();

            if (name.isEmpty()
                    || email.isEmpty()
                    || password.isEmpty()
                    || confirm.isEmpty()) {

                statusLabel.setStyle(
                        "-fx-text-fill:red;"
                );

                statusLabel.setText(
                        "Please fill all fields."
                );

                return;
            }

            if (!password.equals(confirm)) {

                statusLabel.setStyle(
                        "-fx-text-fill:red;"
                );

                statusLabel.setText(
                        "Passwords do not match."
                );

                return;
            }

            /*
             * Later, this is where
             * you save the user to
             * your database.
             */

            Alert alert =
                    new Alert(
                            Alert.AlertType.INFORMATION
                    );

            alert.setHeaderText(null);
            alert.setContentText(
                    "Registration Successful!"
            );

            alert.showAndWait();

            // Save token
            TokenStorage.saveToken(
                    "logged_in"
            );

            // Open dashboard
            try {
                new DashboardForm()
                        .start(stage);
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        });

        // =========================
        // BACK TO LOGIN
        // =========================
        loginLink.setOnAction(e -> {
            try {
                new LoginScreen()
                        .start(stage);
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        });

        Scene scene =
                new Scene(root, 600, 700);

        stage.setTitle(
                "Registration"
        );
        stage.setScene(scene);
        stage.show();
    }
}