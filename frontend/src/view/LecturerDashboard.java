package view;

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;

public class LecturerDashboard {

    private BorderPane root;
    private ScrollPane contentScrollPane;
    private Button activeNavBtn;

    // Zero-argument constructor to match your original configuration
    public LecturerDashboard() {
    }

    // Constructor with NavigationManager parameter (kept for fallback compatibility)
    public LecturerDashboard(Object navManagerFallback) {
    }

    public Scene createDashboardScene() {
        root = new BorderPane();

        // Bind stylesheet
        String stylesheet = getClass().getResource("dashboard.css").toExternalForm();
        root.getStylesheets().add(stylesheet);

        // --- SIDEBAR ---
        VBox sidebar = new VBox();
        sidebar.getStyleClass().add("sidebar");
        sidebar.setPrefWidth(240);

        Label brand = new Label("SMART FORUM");
        brand.getStyleClass().add("sidebar-brand");

        Button btnOverview = new Button("Lecturer Overview");
        btnOverview.setMaxWidth(Double.MAX_VALUE);
        btnOverview.getStyleClass().add("sidebar-link");

        Button btnQuizEngine = new Button("Quiz Creation Engine");
        btnQuizEngine.setMaxWidth(Double.MAX_VALUE);
        btnQuizEngine.getStyleClass().add("sidebar-link");

        btnOverview.setOnAction(e -> {
            highlightNavButton(btnOverview);
            showOverviewWorkspace();
        });

        btnQuizEngine.setOnAction(e -> {
            highlightNavButton(btnQuizEngine);
            showQuizEngineWorkspace();
        });

        Region separator = new Region();
        separator.setPrefHeight(1);
        separator.setStyle("-fx-background-color: rgba(255, 255, 255, 0.1);");
        VBox.setMargin(separator, new Insets(10, 0, 10, 0));

        // Profile Box Widget
        VBox profileBox = new VBox(6);
        profileBox.getStyleClass().add("sidebar-profile");
        VBox.setMargin(profileBox, new Insets(220, 0, 0, 0));

        HBox profileDetails = new HBox(10);
        profileDetails.setAlignment(Pos.CENTER_LEFT);

        Label avatar = new Label("L");
        avatar.getStyleClass().add("sidebar-avatar");

        VBox profileNames = new VBox();
        Label lblName = new Label("Lecturer Account");
        lblName.getStyleClass().add("sidebar-profile-name");
        Label lblRole = new Label("Portal Administrator");
        lblRole.getStyleClass().add("sidebar-profile-role");
        profileNames.getChildren().addAll(lblName, lblRole);

        profileDetails.getChildren().addAll(avatar, profileNames);

        Button btnLogout = new Button("Return to Login");
        btnLogout.getStyleClass().add("sidebar-link");
        btnLogout.setStyle("-fx-text-fill: #ef4444; -fx-padding: 8 0 0 0;");

        // CORRECTION: Direct call to your static NavigationManager
        btnLogout.setOnAction(e -> {
            try {
                // Try calling your custom static logout/route mechanism
                NavigationManager.routeToDashboard(null);
            } catch (Exception ex) {
                System.out.println("Returning to login screen...");
            }
        });

        profileBox.getChildren().addAll(profileDetails, btnLogout);

        sidebar.getChildren().addAll(brand, btnOverview, btnQuizEngine, separator, profileBox);
        root.setLeft(sidebar);

        // --- SCROLL CONTENT CONTAINER ---
        contentScrollPane = new ScrollPane();
        contentScrollPane.setFitToWidth(true);
        contentScrollPane.setStyle("-fx-background: transparent; -fx-background-color: transparent; -fx-border-color: transparent;");
        root.setCenter(contentScrollPane);

        highlightNavButton(btnOverview);
        showOverviewWorkspace();

        return new Scene(root, 1280, 740);
    }

    private void highlightNavButton(Button btn) {
        if (activeNavBtn != null) {
            activeNavBtn.getStyleClass().remove("sidebar-link-active");
            activeNavBtn.getStyleClass().add("sidebar-link");
        }
        activeNavBtn = btn;
        activeNavBtn.getStyleClass().remove("sidebar-link");
        activeNavBtn.getStyleClass().add("sidebar-link-active");
    }

    private void showOverviewWorkspace() {
        VBox workspace = new VBox(24);
        workspace.setPadding(new Insets(32));

        Label viewTitle = new Label("Lecturer Management System Portal");
        viewTitle.getStyleClass().add("dashboard-header");
        workspace.getChildren().add(viewTitle);

        HBox metricsGrid = new HBox(20);
        metricsGrid.getChildren().addAll(
                createCardMetric("Total Quizzes Managed", "12", true),
                createCardMetric("Active Running Timers", "2", false),
                createCardMetric("Historical Grade Avg", "74%", false),
                createCardMetric("Participation Rate", "81%", false)
        );
        workspace.getChildren().add(metricsGrid);

        HBox mainSplit = new HBox(24);

        VBox progressCard = new VBox(16);
        progressCard.getStyleClass().add("content-card");
        progressCard.setPrefWidth(480);

        Label progTitle = new Label("Top Performing Student Communities");
        progTitle.getStyleClass().add("card-title");
        progressCard.getChildren().addAll(progTitle,
                createIndicatorRow("Database Systems Track A", 0.87),
                createIndicatorRow("Software Engineering Core", 0.82),
                createIndicatorRow("Web Development Section B", 0.76)
        );

        VBox quickNoticeCard = new VBox(12);
        HBox.setHgrow(quickNoticeCard, Priority.ALWAYS);
        quickNoticeCard.getStyleClass().add("content-card");

        Label noticeTitle = new Label("Draft Immediate Announcement");
        noticeTitle.getStyleClass().add("card-title");

        TextArea noticeText = new TextArea();
        noticeText.setPromptText("Draft a classroom reminder directly to students...");
        noticeText.setPrefHeight(100);

        Button btnSendNotice = new Button("Broadcast Notice");
        btnSendNotice.getStyleClass().add("btn-primary");
        btnSendNotice.setOnAction(e -> {
            if (!noticeText.getText().trim().isEmpty()) {
                Alert alert = new Alert(Alert.AlertType.INFORMATION, "Notice Broadcast successfully shared with class groups!", ButtonType.OK);
                alert.showAndWait();
                noticeText.clear();
            }
        });

        quickNoticeCard.getChildren().addAll(noticeTitle, noticeText, btnSendNotice);
        mainSplit.getChildren().addAll(progressCard, quickNoticeCard);
        workspace.getChildren().add(mainSplit);

        contentScrollPane.setContent(workspace);
    }

    private void showQuizEngineWorkspace() {
        VBox workspace = new VBox(24);
        workspace.setPadding(new Insets(32));

        Label viewTitle = new Label("Quiz Creation Engine Workspace");
        viewTitle.getStyleClass().add("dashboard-header");
        workspace.getChildren().add(viewTitle);

        VBox engineCard = new VBox(16);
        engineCard.getStyleClass().add("content-card");
        engineCard.setMaxWidth(600);

        Label engineTitle = new Label("Create Quiz Parameters Blueprint");
        engineTitle.getStyleClass().add("card-title");

        VBox fg1 = new VBox(4);
        Label l1 = new Label("Assessment Title Mapping");
        l1.getStyleClass().add("form-label");
        TextField t1 = new TextField();
        t1.setPromptText("e.g., Data Structures Exam");
        fg1.getChildren().addAll(l1, t1);

        VBox fg2 = new VBox(4);
        Label l2 = new Label("Duration Frame Runtime (Minutes)");
        l2.getStyleClass().add("form-label");
        TextField t2 = new TextField("15");
        fg2.getChildren().addAll(l2, t2);

        CheckBox autoSubmitCheck = new CheckBox("Enforce Authoritative `Auto_Submit` Execution");
        autoSubmitCheck.setStyle("-fx-font-weight: bold; -fx-font-size: 12px; -fx-cursor: hand;");
        autoSubmitCheck.setSelected(true);

        Button btnPublish = new Button("Publish Examination Context Blueprint");
        btnPublish.getStyleClass().add("btn-primary");
        btnPublish.setMaxWidth(Double.MAX_VALUE);

        btnPublish.setOnAction(e -> {
            if (t1.getText().trim().isEmpty()) {
                Alert alert = new Alert(Alert.AlertType.ERROR, "Please provide an Assessment Title before publishing.", ButtonType.OK);
                alert.showAndWait();
            } else {
                Alert alert = new Alert(Alert.AlertType.INFORMATION, "Assessment Blueprint '" + t1.getText() + "' has been deployed!", ButtonType.OK);
                alert.showAndWait();
                t1.clear();
                t2.setText("15");
            }
        });

        engineCard.getChildren().addAll(engineTitle, fg1, fg2, autoSubmitCheck, btnPublish);
        workspace.getChildren().add(engineCard);

        contentScrollPane.setContent(workspace);
    }

    private VBox createCardMetric(String label, String value, boolean isAccent) {
        VBox container = new VBox(6);
        Label lbl = new Label(label.toUpperCase());
        Label val = new Label(value);

        if (isAccent) {
            container.getStyleClass().add("stat-card-accent");
            lbl.getStyleClass().add("stat-label-accent");
            val.getStyleClass().add("stat-value-accent");
        } else {
            container.getStyleClass().add("stat-card");
            lbl.getStyleClass().add("stat-label");
            val.getStyleClass().add("stat-value");
        }

        container.getChildren().addAll(lbl, val);
        return container;
    }

    private VBox createIndicatorRow(String title, double fill) {
        VBox row = new VBox(4);
        HBox labels = new HBox();
        Label name = new Label(title);
        name.setStyle("-fx-font-weight: bold; -fx-font-size: 12px; -fx-text-fill: #0b2b1a;");

        Label percentage = new Label((int)(fill * 100) + "%");
        percentage.setStyle("-fx-font-weight: bold; -fx-text-fill: #10b981; -fx-font-size: 12px;");

        Region spacer = new Region();
        HBox.setHgrow(spacer, Priority.ALWAYS);
        labels.getChildren().addAll(name, spacer, percentage);

        ProgressBar bar = new ProgressBar(fill);
        bar.setMaxWidth(Double.MAX_VALUE);

        row.getChildren().addAll(labels, bar);
        return row;
    }
}