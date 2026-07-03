package view;

import javafx.application.Application;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Cursor;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.effect.DropShadow;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.*;
import javafx.scene.paint.Color;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.stage.Stage;
import main.java.dto.UserDAO;
import storage.TokenStorage;

import java.io.InputStream;

public class LoginScreen extends Application {

    @Override
    public void start(Stage stage) {

        // ==========================
        // AUTO LOGIN USING TOKEN
        // ==========================
        String token = TokenStorage.getToken();

        if (token != null) {
            try {
                new DashboardForm().start(stage);
                return;
            } catch (Exception e) {
                e.printStackTrace();
            }
        }

        // ==========================
        // LEFT PANEL
        // ==========================
        VBox leftPanel = new VBox(20);
        leftPanel.setAlignment(Pos.CENTER);
        leftPanel.setPrefWidth(500);
        leftPanel.setStyle(
                "-fx-background-color: linear-gradient(to bottom, #2196F3, #0D47A1);"
        );

        Label forumTitle =
                new Label("Smart Discussion Forum");

        forumTitle.setFont(
                Font.font("Arial", FontWeight.BOLD, 30)
        );

        forumTitle.setTextFill(Color.WHITE);

        Label welcome =
                new Label("Welcome Back!");

        welcome.setFont(
                Font.font("Arial", FontWeight.BOLD, 24)
        );

        welcome.setTextFill(Color.WHITE);

        Label subtitle =
                new Label(
                        "Collaborate, discuss and learn together."
                );

        subtitle.setTextFill(Color.WHITE);

        // ==========================
        // LOAD IMAGE SAFELY
        // ==========================
        ImageView imageView = new ImageView();

        try {
            InputStream stream =
                    getClass().getResourceAsStream(
                            "/images/students.jpg"
                    );

            if (stream != null) {
                Image image = new Image(stream);
                imageView.setImage(image);
                imageView.setFitWidth(250);
                imageView.setFitHeight(250);
                imageView.setPreserveRatio(true);
            }

        } catch (Exception e) {
            System.out.println(
                    "students.jpg not found."
            );
        }

        leftPanel.getChildren().addAll(
                forumTitle,
                welcome,
                subtitle,
                imageView
        );

        // ==========================
        // RIGHT PANEL
        // ==========================
        VBox rightPanel = new VBox(20);
        rightPanel.setAlignment(Pos.CENTER);
        rightPanel.setPadding(
                new Insets(40)
        );

        Label loginTitle =
                new Label("Login");

        loginTitle.setFont(
                Font.font("Arial", FontWeight.BOLD, 28)
        );

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

        CheckBox rememberMe =
                new CheckBox("Remember Me");

        Button loginButton =
                new Button("Login");

        loginButton.setPrefWidth(300);
        loginButton.setCursor(Cursor.HAND);

        Hyperlink registerLink =
                new Hyperlink(
                        "New user? Register here"
                );

        registerLink.setCursor(
                Cursor.HAND
        );

        Label statusLabel =
                new Label();

        statusLabel.setTextFill(Color.RED);

        // ==========================
        // LOGIN ACTION
        // ==========================
        loginButton.setOnAction(e -> {

            String email =
                    emailField.getText().trim();

            String password =
                    passwordField.getText().trim();

            if (email.isEmpty()
                    || password.isEmpty()) {

                statusLabel.setText(
                        "Please fill all fields."
                );
                return;
            }

            /*
             * Replace this section with
             * your database login.
             */
UserDAO dao =
        new UserDAO();

if (dao.login(
        email,
        password
)) {

    if (rememberMe.isSelected()) {
        TokenStorage.saveToken(
                email
        );
    }

    new DashboardForm().start(stage);

} else {

    statusLabel.setText(
            "Invalid credentials."
    );
} {

                if (rememberMe.isSelected()) {
                    TokenStorage.saveToken(
                            "logged_in"
                    );
                }

                try {
                    new DashboardForm()
                            .start(stage);
                } catch (Exception ex) {
                    ex.printStackTrace();
                }

            } else {

                statusLabel.setText(
                        "Invalid email or password."
                );
            }
        });

        // ==========================
        // OPEN REGISTRATION SCREEN
        // ==========================
        registerLink.setOnAction(e -> {
            try {
                new RegistrationScreen()
                        .start(stage);
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        });

        rightPanel.getChildren().addAll(
                loginTitle,
                emailField,
                passwordField,
                rememberMe,
                loginButton,
                registerLink,
                statusLabel
        );

        // ==========================
        // ROOT LAYOUT
        // ==========================
        HBox root = new HBox();

        leftPanel.setPrefWidth(600);
        HBox.setHgrow(
                rightPanel,
                Priority.ALWAYS
        );

        root.getChildren().addAll(
                leftPanel,
                rightPanel
        );

        root.setEffect(
                new DropShadow()
        );

        Scene scene =
                new Scene(root, 1200, 700);

        stage.setTitle(
                "Smart Discussion Forum"
        );
        stage.setScene(scene);
        stage.setMaximized(true);
        stage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}