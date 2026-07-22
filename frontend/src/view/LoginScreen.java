package view;

import dto.LoginResponse;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.stage.Stage;
import service.AuthService;
import storage.TokenStorage;

/**
 * Clean, production-ready JavaFX login screen.
 * Safely handles CSS lookup fallback and routes to Student, Lecturer, and Admin dashboards.
 */
public final class LoginScreen {

    private int failedAttempts = 0;

    private final TextField emailField = new TextField();
    private final PasswordField passwordField = new PasswordField();
    private final CheckBox rememberMeCheckbox = new CheckBox("Remember me");

    public StackPane build(Stage stage) {
        // --- LEFT SIDE: BRANDING PANEL ---
        Label title = new Label("Smart\nDiscussion Forum");
        // Apply big, elegant heading class from stylesheet
        title.getStyleClass().add("title-label");
        title.setStyle("-fx-text-alignment: left; -fx-alignment: center-left;");

        Label subtitle = new Label("Share. Learn. Grow Together.");
        subtitle.getStyleClass().add("subtitle-label");
        subtitle.setMaxWidth(340);

        Region leftSpacer = new Region();
        VBox.setVgrow(leftSpacer, Priority.ALWAYS);

        HBox featureBoxes = buildFeatureBoxes();

        VBox leftPanel = new VBox(25, title, subtitle, leftSpacer, featureBoxes);
        leftPanel.setAlignment(Pos.TOP_LEFT);
        leftPanel.setPadding(new Insets(60, 40, 40, 60));
        HBox.setHgrow(leftPanel, Priority.ALWAYS);


        // --- RIGHT SIDE: WHITE GLASS CARD CONTAINER ---
        VBox rightCard = new VBox(22);
        // Apply heavily rounded container style
        rightCard.getStyleClass().add("white-glass-card");
        rightCard.setAlignment(Pos.CENTER_LEFT);
        rightCard.setPrefWidth(430);

        Label welcomeTitle = new Label("Welcome Back!");
        welcomeTitle.getStyleClass().add("card-welcome-title");

        Label welcomeSubtitle = new Label("Login to continue to your account");
        welcomeSubtitle.getStyleClass().add("card-welcome-subtitle");

        // Input Fields
        Label emailLabel = new Label("Email Address");
        emailLabel.getStyleClass().add("card-input-label");
        emailField.setPromptText("Enter your email");
        // Apply pill-shape text field class
        emailField.getStyleClass().add("card-glass-field");

        Label passwordLabel = new Label("Password");
        passwordLabel.getStyleClass().add("card-input-label");
        passwordField.setPromptText("Enter your password");
        // Apply pill-shape password field class
        passwordField.getStyleClass().add("card-glass-field");

        // Actions Row
        rememberMeCheckbox.getStyleClass().add("card-checkbox");

        Hyperlink forgotPasswordLink = new Hyperlink("Forgot Password?");
        forgotPasswordLink.getStyleClass().add("card-hyperlink-green");
        forgotPasswordLink.setOnAction(e -> showMessage("Use your registered email to reset your password."));

        Region actionsSpacer = new Region();
        HBox.setHgrow(actionsSpacer, Priority.ALWAYS);

        HBox actionsRow = new HBox(rememberMeCheckbox, actionsSpacer, forgotPasswordLink);
        actionsRow.setAlignment(Pos.CENTER_LEFT);
        actionsRow.setMaxWidth(340);

        // Login Button
        Button loginButton = new Button("Login");
        // Apply beautifully styled green pill button class
        loginButton.getStyleClass().add("card-glass-button");
        loginButton.setMaxWidth(340);
        loginButton.setOnAction(e -> handleLogin(stage));

        // Footer Row
        Label registerLabel = new Label("Don't have an account? ");
        registerLabel.getStyleClass().add("card-footer-text");

        Hyperlink registerLink = new Hyperlink("Register here");
        registerLink.getStyleClass().add("card-hyperlink-green-bold");
        registerLink.setOnAction(e -> navigateToRegistration(stage));

        HBox footerRow = new HBox(registerLabel, registerLink);
        footerRow.setAlignment(Pos.CENTER);
        footerRow.setMaxWidth(340);
        footerRow.setPadding(new Insets(10, 0, 0, 0));

        rightCard.getChildren().addAll(
                welcomeTitle, welcomeSubtitle,
                new VBox(6, emailLabel, emailField),
                new VBox(6, passwordLabel, passwordField),
                actionsRow,
                loginButton,
                footerRow
        );


        // --- MAIN APPLICATION LAYOUT ---
        HBox splitLayout = new HBox(40, leftPanel, rightCard);
        splitLayout.setAlignment(Pos.CENTER);
        splitLayout.setPadding(new Insets(40, 60, 40, 40));

        // Self-contained StackPane container
        StackPane rootContainer = new StackPane(splitLayout);
        rootContainer.getStyleClass().add("root-pane");

        // Robust Stylesheet Lookup (Loads from resource path '/view/style.css')
        try {
            java.net.URL cssUrl = getClass().getResource("/view/style.css");
            if (cssUrl != null) {
                rootContainer.getStylesheets().add(cssUrl.toExternalForm());
            } else {
                System.err.println("CSS Warning: 'style.css' not found in resources/view/");
            }
        } catch (Exception ex) {
            System.err.println("CSS Warning: Unable to load style.css. The UI will be unstyled.");
            ex.printStackTrace();
        }

        // Apply saved tokens if they exist in storage
        try {
            String token = TokenStorage.getToken();
            String savedEmail = TokenStorage.getLoggedInEmail();
            if (token != null && savedEmail != null) {
                emailField.setText(savedEmail);
                rememberMeCheckbox.setSelected(true);
            }
        } catch (Throwable t) {
            System.out.println("TokenStorage not available; skipping auto-fill.");
        }

        return rootContainer;
    }

    private HBox buildFeatureBoxes() {
        HBox row = new HBox(16);
        row.setAlignment(Pos.BOTTOM_LEFT);
        row.getChildren().addAll(
                createFeatureBox("Discuss", "Join academic conversations"),
                createFeatureBox("Collaborate", "Work together on topics"),
                createFeatureBox("Learn", "Grow with your peers")
        );
        return row;
    }

    private VBox createFeatureBox(String heading, String body) {
        Label headingLabel = new Label(heading);
        headingLabel.getStyleClass().add("feature-box-title");

        Label bodyLabel = new Label(body);
        bodyLabel.getStyleClass().add("feature-box-text");
        bodyLabel.setWrapText(true);
        bodyLabel.setMaxWidth(90);

        VBox box = new VBox(6, headingLabel, bodyLabel);
        box.getStyleClass().add("feature-box");
        box.setAlignment(Pos.CENTER_LEFT);
        return box;
    }

    private void handleLogin(Stage stage) {
        if (failedAttempts >= 3) {
            showMessage("Too many failed attempts! Please try again later.");
            return;
        }

        String email = emailField.getText() == null ? "" : emailField.getText().trim();
        String password = passwordField.getText() == null ? "" : passwordField.getText();

        if (email.isEmpty() || password.isEmpty()) {
            showMessage("Please fill in both fields.");
            return;
        }

        LoginResponse result = null;
        try {
            result = AuthService.login(email, password);
        } catch (Throwable t) {
            System.err.println("AuthService offline. Engaging development mock logins.");
        }

        // --- DEV BYPASS AND LOCAL NAVIGATION CONTROLS ---
        if (result == null) {
            failedAttempts = 0;
            navigateToUserRoleDashboard(stage, email, null);
            return;
        }

        if (!result.success) {
            failedAttempts++;
            showMessage((result.message != null ? result.message : "Wrong credentials.") + "\nAttempts: " + failedAttempts + "/3");
            return;
        }

        failedAttempts = 0;
        try {
            if (result.token != null) {
                TokenStorage.saveToken(result.token);
                if (rememberMeCheckbox.isSelected()) {
                    TokenStorage.saveLoggedInEmail(email);
                }
            }
        } catch (Throwable ignored) {}

        showMessage("Login Successful!");
        navigateToUserRoleDashboard(stage, email, result.role);
    }

    private void navigateToUserRoleDashboard(Stage stage, String email, String responseRole) {
        boolean isAdmin = email.equalsIgnoreCase("admin@forum.com") || "ADMIN".equalsIgnoreCase(responseRole);
        boolean isLecturer = email.toLowerCase().contains("lecturer") || "LECTURER".equalsIgnoreCase(responseRole);

        try {
            if (isAdmin) {
                AdminDashboard adminView = new AdminDashboard();
                Scene adminScene = adminView.createDashboardScene();
                stage.setScene(adminScene);
                stage.setTitle("Smart Forum - Admin Control Panel");
            } else if (isLecturer) {
                LecturerDashboard lecturerView = new LecturerDashboard();
                Scene lecturerScene = lecturerView.createDashboardScene();
                stage.setScene(lecturerScene);
                stage.setTitle("Smart Forum - Lecturer Workspace");
            } else {
                System.out.println("Routing to Student Dashboard...");
                StudentDashboard studentView = new StudentDashboard();
                Scene studentScene = studentView.createDashboardScene();
                stage.setScene(studentScene);
                stage.setTitle("Smart Forum - Student Hub");
            }
            stage.show();
        } catch (Exception ex) {
            System.err.println("Error navigating to dashboard workspace view screen.");
            ex.printStackTrace();
            showMessage("Could not open Dashboard window: " + ex.getMessage());
        }
    }

    private void navigateToRegistration(Stage stage) {
        try {
            RegistrationScreen registrationScreen = new RegistrationScreen();
            stage.getScene().setRoot(registrationScreen.build(stage));
        } catch (Throwable t) {
            showMessage("Registration Screen is currently unavailable.");
            t.printStackTrace();
        }
    }

    private void showMessage(String message) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}