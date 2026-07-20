package view; // Make sure this matches your actual package folder name

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;

public class StudentDiscussions {

    private BorderPane mainFrame;
    private VBox groupsPanel;

    /**
     * Call this the same way NavigationManager calls createDashboardScene(),
     * e.g. navigationManager.setScene(new StudentDiscussions().createDiscussionsScene());
     */
    public Scene createDiscussionsScene() {
        mainFrame = new BorderPane();
        mainFrame.getStyleClass().add("root");

        // --- SHARED SIDEBAR ---
        VBox sidebar = Sidebar.build(Sidebar.DISCUSSIONS, "testuser2", "Student");
        mainFrame.setLeft(sidebar);

        // --- GROUPS LIST PANEL ("Your Groups") ---
        groupsPanel = new VBox(14);
        groupsPanel.getStyleClass().add("groups-panel");
        groupsPanel.setPrefWidth(280);
        groupsPanel.setMinWidth(280);
        groupsPanel.setPadding(new Insets(24, 16, 24, 24));

        HBox groupsHeader = new HBox();
        groupsHeader.setAlignment(Pos.CENTER_LEFT);
        Label groupsTitle = new Label("Your Groups");
        groupsTitle.getStyleClass().add("groups-title");

        Region groupsHeaderSpacer = new Region();
        HBox.setHgrow(groupsHeaderSpacer, Priority.ALWAYS);

        Button addGroupBtn = new Button("+");
        addGroupBtn.getStyleClass().add("icon-btn-circle");
        Button collapseBtn = new Button("«");
        collapseBtn.getStyleClass().add("icon-btn-plain");

        groupsHeader.getChildren().addAll(groupsTitle, groupsHeaderSpacer, addGroupBtn, collapseBtn);

        TextField searchField = new TextField();
        searchField.setPromptText("Search topics...");
        searchField.getStyleClass().add("groups-search");

        VBox groupList = new VBox(2);
        // Sample/placeholder group names — replace with your real group data source
        String[] sampleGroups = {"Advanced Math", "Group2", "First group", "First group"};
        for (String name : sampleGroups) {
            groupList.getChildren().add(buildGroupItem(name));
        }

        groupsPanel.getChildren().addAll(groupsHeader, searchField, groupList);
        mainFrame.setCenter(null); // placeholder, replaced below by the split layout

        // --- MAIN WELCOME AREA ---
        VBox welcomeArea = new VBox(20);
        welcomeArea.setAlignment(Pos.CENTER);
        welcomeArea.getStyleClass().add("welcome-area");
        welcomeArea.setPadding(new Insets(40));

        Label welcomeIcon = new Label("💬");
        welcomeIcon.getStyleClass().add("welcome-icon-circle");

        Label welcomeTitle = new Label("Welcome to Discussions");
        welcomeTitle.getStyleClass().add("welcome-title");

        Label welcomeSubtitle = new Label(
                "Select a topic from the sidebar to start chatting, or explore\nnew groups to expand your network.");
        welcomeSubtitle.getStyleClass().add("welcome-subtitle");
        welcomeSubtitle.setTextAlignment(javafx.scene.text.TextAlignment.CENTER);

        HBox actionCards = new HBox(20);
        actionCards.setAlignment(Pos.CENTER);
        actionCards.getChildren().addAll(
                buildActionCard("＋", "Create a Group",
                        "Start a new study circle, define your topics, and manage members.", null),
                buildActionCard("🔍", "Join a Group",
                        "Browse existing active groups and join the ongoing academic discussions.", null),
                buildActionCard("✉", "Invite Discussants",
                        "Send an email invitation link to peers to directly join your specific topic.", "SOON")
        );

        welcomeArea.getChildren().addAll(welcomeIcon, welcomeTitle, welcomeSubtitle, actionCards);
        HBox.setHgrow(welcomeArea, Priority.ALWAYS);

        // --- SPLIT LAYOUT: groups list beside welcome area ---
        HBox contentSplit = new HBox();
        contentSplit.getChildren().addAll(groupsPanel, welcomeArea);
        HBox.setHgrow(welcomeArea, Priority.ALWAYS);

        mainFrame.setCenter(contentSplit);

        Scene scene = new Scene(mainFrame, 1250, 720);
        attachStylesheet(scene);
        return scene;
    }

    private HBox buildGroupItem(String name) {
        HBox item = new HBox();
        item.getStyleClass().add("group-item");
        item.setAlignment(Pos.CENTER_LEFT);

        Label nameLabel = new Label(name);
        nameLabel.getStyleClass().add("group-item-name");

        Region spacer = new Region();
        HBox.setHgrow(spacer, Priority.ALWAYS);

        Label chevron = new Label("⌄");
        chevron.getStyleClass().add("group-item-chevron");

        item.getChildren().addAll(nameLabel, spacer, chevron);
        return item;
    }

    private VBox buildActionCard(String icon, String title, String description, String badgeText) {
        VBox card = new VBox(10);
        card.getStyleClass().add("welcome-card");
        card.setAlignment(Pos.TOP_CENTER);
        card.setPrefWidth(220);

        if (badgeText != null) {
            Label badge = new Label(badgeText);
            badge.getStyleClass().add("badge-soon");
            HBox badgeRow = new HBox(badge);
            badgeRow.setAlignment(Pos.CENTER_RIGHT);
            badgeRow.setMaxWidth(Double.MAX_VALUE);
            card.getChildren().add(badgeRow);
        }

        Label iconLabel = new Label(icon);
        iconLabel.getStyleClass().add("welcome-card-icon");

        Label titleLabel = new Label(title);
        titleLabel.getStyleClass().add("welcome-card-title");

        Label descLabel = new Label(description);
        descLabel.getStyleClass().add("welcome-card-desc");
        descLabel.setWrapText(true);
        descLabel.setTextAlignment(javafx.scene.text.TextAlignment.CENTER);

        card.getChildren().addAll(iconLabel, titleLabel, descLabel);
        return card;
    }

    /**
     * Loads dashboard.css if it's on the classpath at /view/dashboard.css.
     * Adjust the resource path below to wherever you place the stylesheet.
     */
    private void attachStylesheet(Scene scene) {
        var css = getClass().getResource("/view/dashboard.css");
        if (css != null) {
            scene.getStylesheets().add(css.toExternalForm());
        }
    }
}