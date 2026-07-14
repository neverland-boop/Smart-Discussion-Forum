package service;

import javafx.application.Platform;
import javafx.stage.Stage;

public class QuizLockService {

    private static boolean quizLocked = false;
    private static int focusViolations = 0;
    private static final int MAX_VIOLATIONS = 3;

    private QuizLockService() {
    }

    public static void lockQuizWindow(Stage stage) {
        if (stage == null) {
            return;
        }

        quizLocked = true;
        focusViolations = 0;

        stage.setFullScreen(true);
        stage.setFullScreenExitHint("");
        stage.setAlwaysOnTop(true);

        stage.setOnCloseRequest(event -> {
            if (quizLocked) {
                event.consume();
                System.out.println("The quiz window cannot be closed.");
            }
        });

        stage.focusedProperty().addListener((observable, oldValue, focused) -> {
            if (quizLocked && !focused) {
                focusViolations++;

                System.out.println(
                        "Quiz focus violation: "
                                + focusViolations
                                + "/"
                                + MAX_VIOLATIONS
                );

                Platform.runLater(() -> {
                    stage.toFront();
                    stage.requestFocus();
                });
            }
        });
    }

    public static void unlockQuizWindow(Stage stage) {
        quizLocked = false;

        if (stage != null) {
            stage.setAlwaysOnTop(false);
            stage.setFullScreen(false);
            stage.setOnCloseRequest(null);
        }
    }

    public static boolean isQuizLocked() {
        return quizLocked;
    }

    public static int getFocusViolations() {
        return focusViolations;
    }

    public static boolean hasReachedViolationLimit() {
        return focusViolations >= MAX_VIOLATIONS;
    }
}