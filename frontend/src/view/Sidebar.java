package view; // Make sure this matches your actual package folder name

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Priority;
import javafx.scene.layout.Region;
import javafx.scene.layout.VBox;

/**
 * Shared sidebar builder used by every student-facing screen so navigation
 * stays pixel-identical everywhere — only the "active" link and the
 * profile info change per screen/user.
 *
 * Usage from any screen:
 *   VBox sidebar = Sidebar.build(Sidebar.DASHBOARD, "testuser2", "Student");
 *   mainFrame.setLeft(sidebar);
 */
public class Sidebar {

    public static final String DASHBOARD    = "Dashboard";
    public static final String DISCUSSIONS  = "Discussions";
    public static final String QUIZZES      = "Quizzes";
    public static final String STUDENTS     = "Students";
    public static final String GRADES       = "Grades";
    public static final String REPORTS      = "Reports";
    public static final String SETTINGS     = "Settings";

    private Sidebar() {}

    public static VBox build(String activePage, String userName, String userRole) {
        VBox sidebar = new VBox();
        sidebar.getStyleClass().add("sidebar");
        sidebar.setPrefWidth(240);
        sidebar.setMinWidth(240);
        sidebar.setPadding(new Insets(24, 16, 20, 16));
        sidebar.setSpacing(4);

        Label brand = new Label("🌿  SMART DISCUSSION");
        brand.getStyleClass().add("sidebar-brand");

        VBox navLinks = new VBox(4);
        navLinks.setPadding(new Insets(24, 0, 0, 0));
        navLinks.getChildren().addAll(
                navButton("🏠", DASHBOARD, activePage),
                navButton("💬", DISCUSSIONS, activePage),
                navButton("📝", QUIZZES, activePage),
                navButton("👥", STUDENTS, activePage),
                navButton("📊", GRADES, activePage),
                navButton("📄", REPORTS, activePage),
                navButton("⚙",  SETTINGS, activePage)
        );

        // Pushes the profile chip to the bottom of the sidebar
        Region spacer = new Region();
        VBox.setVgrow(spacer, Priority.ALWAYS);

        HBox profileChip = new HBox(10);
        profileChip.getStyleClass().add("sidebar-profile");
        profileChip.setAlignment(Pos.CENTER_LEFT);

        String initial = (userName == null || userName.isEmpty()) ? "?" : userName.substring(0, 1).toUpperCase();
        Label avatar = new Label(initial);
        avatar.getStyleClass().add("sidebar-avatar");

        VBox profileText = new VBox(0);
        Label nameLabel = new Label(userName == null ? "" : userName);
        nameLabel.getStyleClass().add("sidebar-profile-name");
        Label roleLabel = new Label(userRole == null ? "" : userRole);
        roleLabel.getStyleClass().add("sidebar-profile-role");
        profileText.getChildren().addAll(nameLabel, roleLabel);

        Region profileSpacer = new Region();
        HBox.setHgrow(profileSpacer, Priority.ALWAYS);

        Label logout = new Label("⏻");
        logout.getStyleClass().add("sidebar-logout");

        profileChip.getChildren().addAll(avatar, profileText, profileSpacer, logout);

        sidebar.getChildren().addAll(brand, navLinks, spacer, profileChip);
        return sidebar;
    }

    private static Button navButton(String icon, String label, String activePage) {
        Button btn = new Button(icon + "   " + label);
        btn.setMaxWidth(Double.MAX_VALUE);
        btn.getStyleClass().add("sidebar-link");
        if (label.equals(activePage)) {
            btn.getStyleClass().add("sidebar-link-active");
        }
        return btn;
    }
}