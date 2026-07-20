package view;

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.control.Hyperlink;
import javafx.scene.control.Label;
import javafx.scene.layout.HBox;
import javafx.scene.layout.StackPane;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

/**
 * Shown when a student attempts to register without agreeing to platform rules.
 */
public final class DeclinedScreen {

    public StackPane build(Stage stage) {
        Label title = new Label("Registration Declined");
        title.getStyleClass().add("declined-title");

        Label message1 = new Label("You must accept the platform rules and guidelines");
        message1.getStyleClass().add("declined-message");

        Label message2 = new Label("before using the Smart Discussion Forum.");
        message2.getStyleClass().add("declined-message");

        Label prompt = new Label("Click here to");
        prompt.getStyleClass().add("hint-label");

        Hyperlink returnLink = new Hyperlink("Return to Register");
        returnLink.getStyleClass().add("glass-hyperlink");
        returnLink.setOnAction(e -> navigateToRegistration(stage));

        HBox returnRow = new HBox(6, prompt, returnLink);
        returnRow.setAlignment(Pos.CENTER);

        VBox card = new VBox(16, title, message1, message2, returnRow);
        card.getStyleClass().add("declined-card");
        card.setAlignment(Pos.CENTER);

        VBox content = SceneHelper.centeredContent(card);
        content.setPadding(new Insets(40, 20, 40, 20));

        content.setStyle("-fx-background-color: #d8eedf;");
        StackPane background = SceneHelper.createBlurredBackground(content);

        background.setStyle("-fx-background-color: #d8eedf;");
        background.getStyleClass().add("mint-root-pane");
        return background;
    }

    private void navigateToRegistration(Stage stage) {
        RegistrationScreen registrationScreen = new RegistrationScreen();
        stage.getScene().setRoot(registrationScreen.build(stage));
        SceneHelper.applyStyles(stage);
    }
}

