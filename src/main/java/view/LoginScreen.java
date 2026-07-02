package view;

import java.util.UUID;

import javafx.application.Application;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.geometry.Rectangle2D;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.*;
import javafx.scene.paint.Color;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.stage.Stage;
import storage.TokenStorage;

public class LoginScreen extends Application {

    private int failedAttempts = 0;

    @Override
    public void start(Stage stage) {

        // Check Remember Me token
        String token = TokenStorage.getToken();

        if (token != null) {
            showMessage("Welcome back!\nYou were remembered.");
        }

        // ==========================
        // LEFT PANEL
        // ==========================

        VBox leftPane = new VBox(20);
        leftPane.setPadding(new Insets(40));
        leftPane.setPrefWidth(400);
        leftPane.setAlignment(Pos.TOP_CENTER);

        leftPane.setStyle(
                "-fx-background-color: linear-gradient(to bottom right, #013220, #008037);" +
                "-fx-background-radius: 20;"
        );

        Label forumTitle = new Label("SMART\nDISCUSSION FORUM");
        forumTitle.setTextFill(Color.WHITE);
        forumTitle.setFont(Font.font("Arial", FontWeight.BOLD, 30));

        Label slogan = new Label(
                "Share. Learn. Grow Together."
        );
        slogan.setTextFill(Color.LIGHTGREEN);
        slogan.setFont(Font.font(18));

        // ==================================
        // RESERVED FOR YOUR OWN IMAGE
        // ==================================

        Image studentImage = new Image(
                getClass().getResourceAsStream("/images/students.jpg")
        );

        ImageView picture = new ImageView(studentImage);
        picture.setFitWidth(256);
        picture.setFitHeight(256);
        picture.setPreserveRatio(true);
        picture.setSmooth(true);

        double imageWidth = studentImage.getWidth();
        double imageHeight = studentImage.getHeight();
        double side = Math.min(imageWidth, imageHeight);
        double x = (imageWidth - side) / 2;
        double y = (imageHeight - side) / 2;
        picture.setViewport(new Rectangle2D(x, y, side, side));

        StackPane imageContainer =
                new StackPane();

        imageContainer.setPrefSize(260, 260);
        imageContainer.setAlignment(Pos.CENTER);

        imageContainer.setStyle(
                "-fx-border-color: white;" +
                "-fx-border-width: 2;" +
                "-fx-border-style: dashed;" +
                "-fx-border-radius: 10;"
        );

        imageContainer.getChildren().add(picture);

        Label studentLogin =
                new Label("Students Login");

        studentLogin.setTextFill(Color.WHITE);
        studentLogin.setFont(
                Font.font("Arial",
                        FontWeight.BOLD,
                        30)
        );

        Label studentText =
                new Label(
                        "Connect, Discuss, Succeed."
                );

        studentText.setTextFill(Color.WHITE);
        studentText.setFont(Font.font(20));

        leftPane.getChildren().addAll(
                forumTitle,
                slogan,
                imageContainer,
                studentLogin,
                studentText
        );

        // ==========================
        // RIGHT PANEL
        // ==========================

        VBox rightPane = new VBox(20);
        rightPane.setPadding(new Insets(50));
        rightPane.setAlignment(Pos.CENTER_LEFT);

        rightPane.setStyle(
                "-fx-background-color: white;" +
                "-fx-background-radius: 20;"
        );

        Label welcome =
                new Label("Welcome Back!");

        welcome.setFont(
                Font.font(
                        "Arial",
                        FontWeight.BOLD,
                        40
                )
        );

        welcome.setTextFill(
                Color.web("#014421")
        );

        Label subtitle =
                new Label(
                        "Login to continue to your account"
                );

        subtitle.setFont(Font.font(20));

        Label emailLabel =
                new Label("Email");

        TextField emailField =
                new TextField();

        emailField.setPromptText(
                "Enter your email"
        );

        emailField.setPrefHeight(45);

        Label passwordLabel =
                new Label("Password");

        PasswordField passwordField =
                new PasswordField();

        passwordField.setPromptText(
                "Enter your password"
        );

        passwordField.setPrefHeight(45);

        CheckBox rememberMe =
                new CheckBox("Remember me");

        Hyperlink forgot =
                new Hyperlink(
                        "Forgot Password?"
                );

        HBox options =
                new HBox(120);

        options.getChildren().addAll(
                rememberMe,
                forgot
        );

        Button loginButton =
                new Button("Login");

        loginButton.setPrefWidth(400);
        loginButton.setPrefHeight(50);

        loginButton.setStyle(
                "-fx-background-color:#0a8f2e;" +
                "-fx-text-fill:white;" +
                "-fx-font-size:18px;" +
                "-fx-font-weight:bold;" +
                "-fx-background-radius:10;"
        );

        Label registerText =
                new Label(
                        "Don't have an account?"
                );

        Hyperlink registerLink =
                new Hyperlink(
                        "Register here"
                );

        HBox registerBox =
                new HBox(
                        10,
                        registerText,
                        registerLink
                );

        registerBox.setAlignment(
                Pos.CENTER
        );

        // ==========================
        // LOGIN ACTION
        // ==========================

        loginButton.setOnAction(e -> {

            if (failedAttempts >= 3) {
                showMessage(
                        "Too many failed attempts!"
                );
                loginButton.setDisable(true);
                return;
            }

            String email =
                    emailField.getText();

            String password =
                    passwordField.getText();

            if (email.isEmpty()) {
                showMessage(
                        "Please enter email."
                );
                return;
            }

            if (!email.contains("@")) {
                showMessage(
                        "Invalid email."
                );
                return;
            }

            if (password.isEmpty()) {
                showMessage(
                        "Please enter password."
                );
                return;
            }

            String correctEmail =
                    "admin@gmail.com";

            String correctPassword =
                    "123456";

            if (!email.equals(correctEmail)
                    || !password.equals(correctPassword)) {

                failedAttempts++;

                showMessage(
                        "Wrong credentials.\nAttempts: "
                                + failedAttempts
                                + "/3"
                );
                return;
            }

            failedAttempts = 0;

            String newToken =
                    UUID.randomUUID().toString();

            if (rememberMe.isSelected()) {
                TokenStorage.saveToken(newToken);
            }

            showMessage(
                    "Login Successful!"
            );
        });

        rightPane.getChildren().addAll(
                welcome,
                subtitle,
                emailLabel,
                emailField,
                passwordLabel,
                passwordField,
                options,
                loginButton,
                registerBox
        );

        // ==========================
        // MAIN LAYOUT
        // ==========================

        HBox root =
                new HBox(
                        leftPane,
                        rightPane
                );

        root.setPadding(
                new Insets(20)
        );

        root.setSpacing(20);

        root.setStyle(
                "-fx-background-color:#015f2f;"
        );

        Scene scene =
                new Scene(root,
                        1200,
                        700);

        stage.setTitle(
                "Smart Discussion Forum"
        );

        stage.setScene(scene);
        stage.show();
    }

    private void showMessage(String message) {
        Alert alert =
                new Alert(
                        Alert.AlertType.INFORMATION
                );

        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }

    public static void main(String[] args) {
        launch(args);
    }
}