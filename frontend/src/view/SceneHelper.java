package view;

import javafx.geometry.Pos;
import javafx.scene.Node;
import javafx.scene.effect.GaussianBlur;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.StackPane;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

/**
 * Shared helpers for building glassmorphism screens without Scene Builder.
 */
public final class SceneHelper {

    private static final String STYLESHEET = "/view/style.css";

    private SceneHelper() {
    }

    public static void applyStyles(Stage stage) {
        var resource = SceneHelper.class.getResource(STYLESHEET);
        if (resource != null) {
            String css = resource.toExternalForm();
            if (!stage.getScene().getStylesheets().contains(css)) {
                stage.getScene().getStylesheets().add(css);
            }
        }
    }

    public static StackPane createBlurredBackground(Node content) {
        StackPane root = new StackPane();
        root.getStyleClass().add("root-pane");
        root.setPrefSize(1000, 700);

        try {
            var imageStream = SceneHelper.class.getResourceAsStream("/images/background.png");
            if (imageStream != null) {
                Image bgImage = new Image(imageStream, 1000, 700, false, true, true);

                ImageView blurredBg = new ImageView(bgImage);
                blurredBg.setFitWidth(1000);
                blurredBg.setFitHeight(700);
                blurredBg.setPreserveRatio(false);
                blurredBg.setEffect(new GaussianBlur(18));

                ImageView sharpBg = new ImageView(bgImage);
                sharpBg.setFitWidth(1000);
                sharpBg.setFitHeight(700);
                sharpBg.setPreserveRatio(false);
                sharpBg.setOpacity(0.35);

                root.getChildren().addAll(blurredBg, sharpBg, content);
            } else {
                throw new Exception("Background image not found");
            }
        } catch (Exception ex) {
            javafx.scene.layout.Region gradient = new javafx.scene.layout.Region();
            gradient.setStyle(
                    "-fx-background-color: linear-gradient(to bottom, #2d4a2d, #1a2e1a, #0f1f0f);"
            );
            gradient.setPrefSize(1000, 700);
            root.getChildren().addAll(gradient, content);
        }

        return root;
    }

    public static VBox centeredContent(Node... children) {
        VBox box = new VBox(14);
        box.setAlignment(Pos.CENTER);
        box.getChildren().addAll(children);
        return box;
    }
}
