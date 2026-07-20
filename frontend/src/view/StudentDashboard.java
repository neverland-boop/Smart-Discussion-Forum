package view; // Make sure this matches your actual package folder name

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;

public class StudentDashboard {

    private StackPane rootLayoutContainer;
    private BorderPane mainDashboardFrame;
    private VBox lockoutInterceptorOverlay;

    /**
     * This is the exact method the NavigationManager calls to load this screen.
     */
    public Scene createDashboardScene() {
        rootLayoutContainer = new StackPane();
        rootLayoutContainer.getStyleClass().add("root");
        mainDashboardFrame = new BorderPane();

        // --- STUDENT NAVIGATION SIDEBAR (shared component) ---
        VBox sidebar = Sidebar.build(Sidebar.DASHBOARD, "testuser2", "Student");
        mainDashboardFrame.setLeft(sidebar);

        // --- STUDENT CONTENT INNER WORKSPACE ---
        VBox workspace = new VBox(18);
        workspace.setPadding(new Insets(28));

        // Top row: page title + "Join a Group" action, like the screenshot
        HBox topBar = new HBox();
        topBar.setAlignment(Pos.CENTER_LEFT);

        Label header = new Label("Dashboard Overview");
        header.getStyleClass().add("dashboard-header");

        Region topSpacer = new Region();
        HBox.setHgrow(topSpacer, Priority.ALWAYS);

        Button joinGroupBtn = new Button("+  Join a Group");
        joinGroupBtn.getStyleClass().add("btn-primary");

        topBar.getChildren().addAll(header, topSpacer, joinGroupBtn);

        // Stats row (Active Groups, Pending Quizzes, Avg Score, Unread)
        HBox statsRow = new HBox(12);
        statsRow.getStyleClass().add("stats-row");

        VBox stat1 = buildStatCard("ACTIVE GROUPS", "0", "🧑‍🤝‍🧑");
        VBox stat2 = buildStatCard("PENDING QUIZZES", "0", "📋");
        VBox stat3 = buildStatCard("AVG. SCORE", "-", "📈");
        VBox stat4 = buildStatCard("UNREAD MSGS", "0", "💬");

        statsRow.getChildren().addAll(stat1, stat2, stat3, stat4);
        HBox.setHgrow(stat1, Priority.ALWAYS);
        HBox.setHgrow(stat2, Priority.ALWAYS);
        HBox.setHgrow(stat3, Priority.ALWAYS);
        HBox.setHgrow(stat4, Priority.ALWAYS);

        // Main panels lower
        HBox mainPanels = new HBox(16);

        VBox leftPanel = new VBox(12);
        leftPanel.getStyleClass().add("content-card");
        leftPanel.setPrefWidth(760);
        Label leftTitle = new Label("My Groups");
        leftTitle.getStyleClass().add("card-title");
        Label noGroups = new Label("No active groups. Browse groups →");
        noGroups.getStyleClass().addAll("muted", "list-empty");
        leftPanel.getChildren().addAll(leftTitle, noGroups);

        VBox rightPanel = new VBox(12);
        rightPanel.setPrefWidth(360);
        rightPanel.getStyleClass().add("content-card");
        Label rightTitle = new Label("Upcoming Quizzes");
        rightTitle.getStyleClass().add("card-title");
        Label noQuizzes = new Label("No pending quizzes.");
        noQuizzes.getStyleClass().addAll("muted", "list-empty");
        rightPanel.getChildren().addAll(rightTitle, noQuizzes);

        mainPanels.getChildren().addAll(leftPanel, rightPanel);

        // Recent Activity panel (full width, seen at the bottom of the screenshot)
        VBox activityPanel = new VBox(12);
        activityPanel.getStyleClass().add("content-card");
        Label activityTitle = new Label("Recent Activity");
        activityTitle.getStyleClass().add("card-title");
        Label activityEmpty = new Label("Activity log is empty.");
        activityEmpty.getStyleClass().addAll("muted", "list-empty");
        activityPanel.getChildren().addAll(activityTitle, activityEmpty);

        workspace.getChildren().addAll(topBar, statsRow, mainPanels, activityPanel);
        mainDashboardFrame.setCenter(workspace);

        // --- FIGURE 6.6 DISCIPLINARY LOCKOUT ACCOUNT INTERCEPTOR OVERLAY ---
        lockoutInterceptorOverlay = new VBox(16);
        lockoutInterceptorOverlay.setAlignment(Pos.CENTER);
        lockoutInterceptorOverlay.setStyle("-fx-background-color: #ffffff; -fx-padding: 40;");

        VBox lockAlertBox = new VBox(16);
        lockAlertBox.setMaxWidth(500);
        lockAlertBox.setAlignment(Pos.CENTER);
        lockAlertBox.setStyle("-fx-border-color: #ef4444; -fx-border-width: 2; -fx-border-radius: 12; -fx-padding: 32;");

        Label lockIcon = new Label("🛑 Account Restricted");
        lockIcon.setStyle("-fx-font-size: 28px; -fx-font-weight: bold; -fx-text-fill: #ef4444;");
        Label lockDesc = new Label("Your access credentials have been restricted for violating core protocol terms.");
        lockDesc.setWrapText(true);
        lockDesc.setStyle("-fx-text-fill: #64748b; -fx-alignment: center;");

        lockAlertBox.getChildren().addAll(lockIcon, lockDesc);
        lockoutInterceptorOverlay.getChildren().add(lockAlertBox);

        // Assemble layers into the stack frame layout
        rootLayoutContainer.getChildren().addAll(mainDashboardFrame, lockoutInterceptorOverlay);
        lockoutInterceptorOverlay.setVisible(false); // Switch to true if simulating an infraction restriction

        Scene scene = new Scene(rootLayoutContainer, 1250, 720);
        attachStylesheet(scene);
        return scene;
    }

    private VBox buildStatCard(String label, String value, String icon) {
        VBox card = new VBox(4);
        card.getStyleClass().add("stat-card");

        HBox topRow = new HBox();
        topRow.setAlignment(Pos.CENTER_LEFT);
        Label labelNode = new Label(label);
        labelNode.getStyleClass().add("stat-label");

        Region spacer = new Region();
        HBox.setHgrow(spacer, Priority.ALWAYS);

        Label iconNode = new Label(icon);
        iconNode.getStyleClass().add("stat-icon");

        topRow.getChildren().addAll(labelNode, spacer, iconNode);

        Label valueNode = new Label(value);
        valueNode.getStyleClass().add("stat-value");

        card.getChildren().addAll(topRow, valueNode);
        return card;
    }

    /**
     * Loads dashboard.css from src/main/resources/dashboard.css
     */
    private void attachStylesheet(Scene scene) {
        var css = getClass().getResource("/dashboard.css");
        if (css != null) {
            scene.getStylesheets().add(css.toExternalForm());
        }
    }

    /**
     * Call this method from your controllers anytime you want to trigger
     * an administrative block on this student's UI interface.
     */
    public void setDisciplinaryAccessLockoutActive(boolean isActive) {
        if (lockoutInterceptorOverlay != null && mainDashboardFrame != null) {
            lockoutInterceptorOverlay.setVisible(isActive);
            mainDashboardFrame.setDisable(isActive);
        }
    }
}