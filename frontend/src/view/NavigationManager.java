package view;

import javafx.scene.Scene;
import javafx.stage.Stage;

public class NavigationManager {

    private static Stage primaryStage;

    /**
     * Called in your Main.java start method: NavigationManager.init(primaryStage);
     */
    public static void init(Stage stage) {
        primaryStage = stage;
    }

    /**
     * Switches the primary stage scene based on the user's role.
     */
    public static void routeToDashboard(UserSession session) {
        if (primaryStage == null) {
            System.err.println("NavigationManager error: Stage not initialized. Call NavigationManager.init(stage) first.");
            return;
        }

        if (session == null) {
            // If session is null, default to Login Screen
            navigateToLogin();
            return;
        }

        switch (session.getRole()) {
            case ADMIN:
                navigateToAdminDashboard();
                break;
            case LECTURER:
                navigateToLecturerDashboard();
                break;
            default:
                System.out.println("Student dashboard routing is pending implementation.");
                break;
        }
    }

    public static void navigateToAdminDashboard() {
        AdminDashboard adminView = new AdminDashboard();
        Scene scene = adminView.createDashboardScene();
        primaryStage.setScene(scene);
        primaryStage.setTitle("Smart Forum - Admin Panel");
        primaryStage.show();
    }

    public static void navigateToLecturerDashboard() {
        LecturerDashboard lecturerView = new LecturerDashboard();
        Scene scene = lecturerView.createDashboardScene();
        primaryStage.setScene(scene);
        primaryStage.setTitle("Smart Forum - Lecturer Workspace");
        primaryStage.show();
    }

    public static void navigateToLogin() {
        try {
            LoginScreen loginScreen = new LoginScreen();
            Scene scene = new Scene(loginScreen.build(primaryStage), 1000, 700);
            primaryStage.setScene(scene);
            primaryStage.setTitle("Smart Forum - Login");
            primaryStage.show();
        } catch (Exception e) {
            System.err.println("Could not load login screen: " + e.getMessage());
        }
    }
}