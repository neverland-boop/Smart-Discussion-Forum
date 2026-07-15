package view;

import javafx.beans.property.SimpleStringProperty;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;

public class AdminDashboard {

    private BorderPane root;
    private ScrollPane contentScrollPane;
    private Button activeNavBtn;

    public AdminDashboard() {
    }

    public AdminDashboard(Object navManagerFallback) {
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

        Label brand = new Label("SMART ADMIN");
        brand.getStyleClass().add("sidebar-brand");

        Button btnOverview = new Button("Dashboard Overview");
        Button btnMembers = new Button("Member Management");
        Button btnQuizzes = new Button("Quiz Controls");

        btnOverview.setMaxWidth(Double.MAX_VALUE);
        btnMembers.setMaxWidth(Double.MAX_VALUE);
        btnQuizzes.setMaxWidth(Double.MAX_VALUE);

        btnOverview.getStyleClass().add("sidebar-link");
        btnMembers.getStyleClass().add("sidebar-link");
        btnQuizzes.getStyleClass().add("sidebar-link");

        btnOverview.setOnAction(e -> {
            highlightNavButton(btnOverview);
            showOverviewWorkspace();
        });

        btnMembers.setOnAction(e -> {
            highlightNavButton(btnMembers);
            showMembersWorkspace();
        });

        btnQuizzes.setOnAction(e -> {
            highlightNavButton(btnQuizzes);
            showQuizControlsWorkspace();
        });

        Region separator = new Region();
        separator.setPrefHeight(1);
        separator.setStyle("-fx-background-color: rgba(255, 255, 255, 0.1);");
        VBox.setMargin(separator, new Insets(10, 0, 10, 0));

        // Bottom User Account Control Widget
        VBox profileBox = new VBox(6);
        profileBox.getStyleClass().add("sidebar-profile");
        VBox.setMargin(profileBox, new Insets(240, 0, 0, 0));

        HBox profileDetails = new HBox(10);
        profileDetails.setAlignment(Pos.CENTER_LEFT);

        Label avatar = new Label("A");
        avatar.getStyleClass().add("sidebar-avatar");

        VBox profileNames = new VBox();
        Label lblName = new Label("Administrator");
        lblName.getStyleClass().add("sidebar-profile-name");
        Label lblRole = new Label("Security Overseer");
        lblRole.getStyleClass().add("sidebar-profile-role");
        profileNames.getChildren().addAll(lblName, lblRole);

        profileDetails.getChildren().addAll(avatar, profileNames);

        Button btnLogout = new Button("Return to Login");
        btnLogout.getStyleClass().add("sidebar-link");
        btnLogout.setStyle("-fx-text-fill: #ef4444; -fx-padding: 8 0 0 0;");

        // CORRECTION: Direct static call
        btnLogout.setOnAction(e -> {
            try {
                NavigationManager.routeToDashboard(null);
            } catch (Exception ex) {
                System.out.println("Returning to login screen...");
            }
        });

        profileBox.getChildren().addAll(profileDetails, btnLogout);

        sidebar.getChildren().addAll(brand, btnOverview, btnMembers, btnQuizzes, separator, profileBox);
        root.setLeft(sidebar);

        // --- MAIN WORKSPACE SCROLL CONTAINER ---
        contentScrollPane = new ScrollPane();
        contentScrollPane.setFitToWidth(true);
        contentScrollPane.setStyle("-fx-background: transparent; -fx-background-color: transparent; -fx-border-color: transparent;");
        root.setCenter(contentScrollPane);

        highlightNavButton(btnOverview);
        showOverviewWorkspace();

        return new Scene(root, 1280, 760);
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

        HBox header = new HBox();
        header.setAlignment(Pos.CENTER_LEFT);
        Label viewTitle = new Label("System Dashboard Administration");
        viewTitle.getStyleClass().add("dashboard-header");

        Region spacer = new Region();
        HBox.setHgrow(spacer, Priority.ALWAYS);

        Button btnExport = new Button("Export Activity Logs");
        btnExport.getStyleClass().add("btn-primary");
        btnExport.setOnAction(e -> {
            Alert alert = new Alert(Alert.AlertType.INFORMATION, "Success! System Activity Logs exported as CSV.", ButtonType.OK);
            alert.showAndWait();
        });

        header.getChildren().addAll(viewTitle, spacer, btnExport);
        workspace.getChildren().add(header);

        GridPane grid = new GridPane();
        grid.setHgap(24);
        grid.setVgap(24);

        VBox complianceCard = new VBox(16);
        complianceCard.getStyleClass().add("content-card");
        Label compTitle = new Label("Member Platform Compliance Standing Monitoring");
        compTitle.getStyleClass().add("card-title");

        TableView<String[]> complianceTable = new TableView<>();
        TableColumn<String[], String> colUser = new TableColumn<>("User ID Name");
        colUser.setCellValueFactory(data -> new SimpleStringProperty(data.getValue()[0]));
        colUser.setPrefWidth(220);

        TableColumn<String[], String> colRole = new TableColumn<>("Assigned Role");
        colRole.setCellValueFactory(data -> new SimpleStringProperty(data.getValue()[1]));
        colRole.setPrefWidth(120);

        TableColumn<String[], String> colStatus = new TableColumn<>("Standing Status");
        colStatus.setCellValueFactory(data -> new SimpleStringProperty(data.getValue()[2]));
        colStatus.setPrefWidth(120);

        complianceTable.getColumns().addAll(colUser, colRole, colStatus);
        complianceTable.setPrefHeight(180);

        complianceTable.getItems().setAll(
                new String[]{"micheal@smart...", "Student", "Active Good Standing"},
                new String[]{"john@smart...", "Student", "Warning - 1 Point"},
                new String[]{"lecturer.math@smart...", "Lecturer", "Verified Access"},
                new String[]{"esther.a@smart...", "Student", "Blacklisted Account"}
        );

        complianceCard.getChildren().addAll(compTitle, complianceTable);
        GridPane.setHgrow(complianceCard, Priority.ALWAYS);
        grid.add(complianceCard, 0, 0);

        VBox controlsCard = new VBox(12);
        controlsCard.getStyleClass().add("content-card");
        controlsCard.setPrefWidth(350);
        Label ctrlTitle = new Label("Threshold Controls Configuration");
        ctrlTitle.getStyleClass().add("card-title");

        VBox fieldGroup = new VBox(6);
        Label lblInactivity = new Label("Inactivity Multiplier Threshold (Days)");
        lblInactivity.getStyleClass().add("form-label");
        TextField txtInactivity = new TextField("30");
        fieldGroup.getChildren().addAll(lblInactivity, txtInactivity);

        Button btnSaveConfig = new Button("Save System Configurations");
        btnSaveConfig.getStyleClass().add("btn-primary");
        btnSaveConfig.setMaxWidth(Double.MAX_VALUE);
        btnSaveConfig.setOnAction(e -> {
            Alert alert = new Alert(Alert.AlertType.INFORMATION, "Configuration saved successfully! Threshold set to: " + txtInactivity.getText() + " days", ButtonType.OK);
            alert.showAndWait();
        });

        controlsCard.getChildren().addAll(ctrlTitle, fieldGroup, btnSaveConfig);
        grid.add(controlsCard, 1, 0);
        workspace.getChildren().add(grid);

        VBox lowerCard = new VBox(16);
        lowerCard.getStyleClass().add("content-card");
        Label lowerTitle = new Label("Lecturer Enrollment & Global Interception Matrix");
        lowerTitle.getStyleClass().add("card-title");

        HBox lowerSplit = new HBox(40);
        lowerSplit.setAlignment(Pos.CENTER_LEFT);

        VBox enrollBox = new VBox(8);
        enrollBox.setPrefWidth(400);
        TextField txtLecturerName = new TextField();
        txtLecturerName.setPromptText("Lecturer Complete Email");
        Button btnEnroll = new Button("Publish Enrolled Lecturer");
        btnEnroll.getStyleClass().add("btn-primary");
        btnEnroll.setOnAction(e -> {
            String value = txtLecturerName.getText().trim();
            if (value.isEmpty()) {
                Alert alert = new Alert(Alert.AlertType.ERROR, "Lecturer email input cannot be empty.", ButtonType.OK);
                alert.showAndWait();
            } else {
                Alert alert = new Alert(Alert.AlertType.INFORMATION, "Lecturer Profile Pattern Registered: " + value, ButtonType.OK);
                alert.showAndWait();
                txtLecturerName.clear();
            }
        });
        enrollBox.getChildren().addAll(txtLecturerName, btnEnroll);

        VBox overrideBox = new VBox(8);
        Label lblOverride = new Label("Global Emergency Override Interface Constraints:");
        lblOverride.getStyleClass().add("form-label");
        Button btnEmergency = new Button("Emergency Cessation: Suspend All Active Quizzes");
        btnEmergency.getStyleClass().add("btn-danger");
        btnEmergency.setOnAction(e -> {
            Alert confirm = new Alert(Alert.AlertType.CONFIRMATION, "CRITICAL OVERRIDE: Are you sure you want to suspend all live exams running platform-wide?", ButtonType.YES, ButtonType.NO);
            confirm.showAndWait();
            if (confirm.getResult() == ButtonType.YES) {
                Alert okAlert = new Alert(Alert.AlertType.WARNING, "All live classroom evaluations have been forcibly put on hold.", ButtonType.OK);
                okAlert.showAndWait();
            }
        });
        overrideBox.getChildren().addAll(lblOverride, btnEmergency);

        lowerSplit.getChildren().addAll(enrollBox, overrideBox);
        lowerCard.getChildren().addAll(lowerTitle, lowerSplit);
        workspace.getChildren().add(lowerCard);

        contentScrollPane.setContent(workspace);
    }

    private void showMembersWorkspace() {
        VBox workspace = new VBox(24);
        workspace.setPadding(new Insets(32));

        Label viewTitle = new Label("User Access & Behavior Control");
        viewTitle.getStyleClass().add("dashboard-header");
        workspace.getChildren().add(viewTitle);

        VBox wrapTableBox = new VBox(16);
        wrapTableBox.getStyleClass().add("content-card");

        Label subTitle = new Label("Registered Members & Security Actions");
        subTitle.getStyleClass().add("card-title");

        TableView<String[]> membersTable = new TableView<>();
        TableColumn<String[], String> colEmail = new TableColumn<>("Email Address");
        colEmail.setCellValueFactory(data -> new SimpleStringProperty(data.getValue()[0]));
        colEmail.setPrefWidth(250);

        TableColumn<String[], String> colWarnings = new TableColumn<>("Accrued Warning Points");
        colWarnings.setCellValueFactory(data -> new SimpleStringProperty(data.getValue()[1]));
        colWarnings.setPrefWidth(200);

        TableColumn<String[], String> colStatus = new TableColumn<>("Active Status");
        colStatus.setCellValueFactory(data -> new SimpleStringProperty(data.getValue()[2]));
        colStatus.setPrefWidth(150);

        membersTable.getColumns().addAll(colEmail, colWarnings, colStatus);
        membersTable.setPrefHeight(250);

        membersTable.getItems().setAll(
                new String[]{"micheal@smart...", "0 / 3 Points", "Good Standing"},
                new String[]{"john@smart...", "1 / 3 Points", "Good Standing"},
                new String[]{"esther.a@smart...", "3 / 3 Points", "Banned & Blacklisted"}
        );

        HBox quickActions = new HBox(12);
        Button btnIssueWarn = new Button("Issue Incident Warning");
        btnIssueWarn.getStyleClass().add("btn-warning");
        btnIssueWarn.setOnAction(e -> {
            Alert alert = new Alert(Alert.AlertType.INFORMATION, "Warning successfully documented onto the student profile history.", ButtonType.OK);
            alert.showAndWait();
        });

        Button btnBlacklist = new Button("Blacklist User");
        btnBlacklist.getStyleClass().add("btn-danger");
        btnBlacklist.setOnAction(e -> {
            Alert alert = new Alert(Alert.AlertType.WARNING, "Target profile is now formally blacklisted from classroom system activities.", ButtonType.OK);
            alert.showAndWait();
        });

        quickActions.getChildren().addAll(btnIssueWarn, btnBlacklist);

        wrapTableBox.getChildren().addAll(subTitle, membersTable, quickActions);
        workspace.getChildren().add(wrapTableBox);

        contentScrollPane.setContent(workspace);
    }

    private void showQuizControlsWorkspace() {
        VBox workspace = new VBox(24);
        workspace.setPadding(new Insets(32));

        Label viewTitle = new Label("Platform Quiz Supervision Controls");
        viewTitle.getStyleClass().add("dashboard-header");
        workspace.getChildren().add(viewTitle);

        VBox controlBox = new VBox(16);
        controlBox.setMaxWidth(600);
        controlBox.getStyleClass().add("content-card");

        Label desc = new Label("Administrative controls to force submit or clear existing student testing attempts globally.");
        desc.getStyleClass().add("muted");
        desc.setWrapText(true);

        Button btnForceSubmit = new Button("Force Submit All Active Student Quizzes");
        btnForceSubmit.setMaxWidth(Double.MAX_VALUE);
        btnForceSubmit.getStyleClass().add("btn-primary");
        btnForceSubmit.setOnAction(e -> {
            Alert alert = new Alert(Alert.AlertType.INFORMATION, "Operation successful! All ongoing student attempts have been auto-submitted.", ButtonType.OK);
            alert.showAndWait();
        });

        Button btnClearState = new Button("Clear All System Testing Cache Data");
        btnClearState.setMaxWidth(Double.MAX_VALUE);
        btnClearState.getStyleClass().add("btn-outline");
        btnClearState.setOnAction(e -> {
            Alert alert = new Alert(Alert.AlertType.CONFIRMATION, "Are you sure you want to purge all running exam cache states? This is irreversible.", ButtonType.YES, ButtonType.NO);
            alert.showAndWait();
        });

        controlBox.getChildren().addAll(desc, btnForceSubmit, btnClearState);
        workspace.getChildren().add(controlBox);

        contentScrollPane.setContent(workspace);
    }
}