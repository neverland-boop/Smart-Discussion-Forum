package view;

import dto.LoginResponse;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Hyperlink;
import javafx.scene.control.Label;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.scene.layout.HBox;
import javafx.scene.layout.StackPane;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import service.AuthService;
import storage.TokenStorage;

/**
 * Hard-coded registration screen styled with matching glassmorphism inputs.
 * Displays terms and conditions cleanly via a single checkbox + hyperlink layout.
 */
public final class RegistrationScreen {

    private final TextField fullNameField = new TextField();
    private final TextField emailField = new TextField();
    private final PasswordField passwordField = new PasswordField();
    private final PasswordField confirmPasswordField = new PasswordField();
    private final CheckBox rulesCheckbox = new CheckBox("I agree to the ");

    public StackPane build(Stage stage) {
        Label title = new Label("Create Account");
        title.getStyleClass().add("title-label");
        title.getStyleClass().add("mint-title-label");

        Label subtitle = new Label("Onboard to join the forum and start sharing ideas.");
        subtitle.getStyleClass().add("subtitle-label");


        subtitle.setMaxWidth(340);

        fullNameField.setPromptText("Full Name");
        fullNameField.getStyleClass().add("card-glass-field");
        fullNameField.getStyleClass().add("mint-field");


        emailField.setPromptText("Email Address");
        emailField.getStyleClass().add("card-glass-field");
        emailField.getStyleClass().add("mint-field");

        passwordField.setPromptText("Password");
        passwordField.getStyleClass().add("card-glass-field");
       passwordField.getStyleClass().add("mint-field");

        confirmPasswordField.setPromptText("Confirm Password");
        confirmPasswordField.getStyleClass().add("card-glass-field");
        confirmPasswordField.getStyleClass().add("mint-field");

        // --- COMPACT TERMS & CONDITIONS ROW (No inline lists) ---
        rulesCheckbox.getStyleClass().add("card-checkbox");

        Hyperlink rulesLink = new Hyperlink("platform rules and terms");
        rulesLink.getStyleClass().add("card-hyperlink-green");
        rulesLink.setOnAction(e -> showPlatformRules());

        HBox termsRow = new HBox(0, rulesCheckbox, rulesLink);
        termsRow.setAlignment(Pos.CENTER_LEFT);
        termsRow.setMaxWidth(340);
        termsRow.setPadding(new Insets(5, 0, 5, 4));

        // --- SUBMIT & NAVIGATION ---
        Button registerButton = new Button("Register");
        registerButton.getStyleClass().add("card-glass-button");
        registerButton.setOnAction(e -> handleRegister(stage));

        Label loginPrompt = new Label("Already have an account? ");
        loginPrompt.getStyleClass().add("card-footer-text");

        Hyperlink loginLink = new Hyperlink("Login here");
        loginLink.getStyleClass().add("card-hyperlink-green-bold");
        loginLink.setOnAction(e -> navigateToLogin(stage));

        HBox loginRow = new HBox(0, loginPrompt, loginLink);
        loginRow.setAlignment(Pos.CENTER);
        loginRow.setMaxWidth(340);

        VBox rightCard = new VBox(18);
        rightCard.setAlignment(Pos.CENTER);
        rightCard.getChildren().addAll(
                title,
                subtitle,
                fullNameField,
                emailField,
                passwordField,
                confirmPasswordField,
                termsRow,
                registerButton,
                loginRow
        );

        // Keep left-side empty brand spacing structure or align symmetrically inside container
        HBox splitLayout = new HBox(rightCard);
        splitLayout.setAlignment(Pos.CENTER);
        splitLayout.setPadding(new Insets(40));


        splitLayout.setStyle("-fx-background-color: #d8eedf;");
        StackPane background = SceneHelper.createBlurredBackground(splitLayout);

        background.setStyle("-fx-background-color: #d8eedf;");
        background.getStyleClass().add("mint-root-pane");

        return background;
    }

    private void handleRegister(Stage stage) {
        if (!rulesCheckbox.isSelected()) {
            navigateToDeclined(stage);
            return;
        }

        String fullName = trim(fullNameField.getText());
        String email = trim(emailField.getText());
        String password = passwordField.getText() == null ? "" : passwordField.getText();
        String confirmPassword = confirmPasswordField.getText() == null ? "" : confirmPasswordField.getText();

        if (fullName.isEmpty() || email.isEmpty() || password.isEmpty() || confirmPassword.isEmpty()) {
            showMessage("Please fill in all registration fields.");
            return;
        }
        if (!email.contains("@")) {
            showMessage("Please enter a valid email address.");
            return;
        }
        if (!password.equals(confirmPassword)) {
            showMessage("Passwords do not match!");
            return;
        }

        LoginResponse result = AuthService.register(fullName, email, password, confirmPassword, true);

        if (result.success) {
            if (result.token != null) {
                TokenStorage.saveToken(result.token);
                TokenStorage.saveLoggedInEmail(email);
            }
            showMessage("Account registered successfully. Welcome to the Smart Discussion Forum!");
            navigateToLogin(stage);
        } else {
            showMessage(result.message != null ? result.message : "Registration failed.");
        }
    }

    private void showPlatformRules() {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle("Platform Rules");
        alert.setHeaderText("Smart Discussion Forum — Platform Rules");

        VBox rulesBox = new VBox(8);
        String[] rules = {
                "1. Be respectful to all members at all times.",
                "2. No spam, harassment, or irrelevant content.",
                "3. Use the platform for academic discussions only.",
                "4. Do not share confidential or copyrighted material without permission.",
                "5. Violations may lead to warnings, suspension, or permanent bans."
        };
        for (String rule : rules) {
            Label label = new Label(rule);
            label.getStyleClass().add("rules-dialog-item");
            label.setWrapText(true);
            rulesBox.getChildren().add(label);
        }

        alert.getDialogPane().setContent(rulesBox);
        alert.showAndWait();
    }

    private void navigateToLogin(Stage stage) {
        LoginScreen loginScreen = new LoginScreen();
        stage.getScene().setRoot(loginScreen.build(stage));
        SceneHelper.applyStyles(stage);
    }

    private void navigateToDeclined(Stage stage) {
        DeclinedScreen declinedScreen = new DeclinedScreen();
        stage.getScene().setRoot(declinedScreen.build(stage));
        SceneHelper.applyStyles(stage);
    }

    private String trim(String value) {
        return value == null ? "" : value.trim();
    }

    private void showMessage(String message) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}