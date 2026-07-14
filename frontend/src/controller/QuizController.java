package controller;

import javafx.fxml.FXML;
import javafx.stage.Stage;
import service.QuizLockService;

public class QuizController {

    private Stage quizStage;

    public void startQuiz(Stage stage) {
        if (stage == null) {
            System.out.println("Quiz stage was not provided.");
            return;
        }

        quizStage = stage;
        QuizLockService.lockQuizWindow(quizStage);

        System.out.println("Quiz lock activated.");
    }

    @FXML
    private void submitQuiz() {
        finishQuiz();
    }

    public void checkViolations() {
        if (QuizLockService.hasReachedViolationLimit()) {
            System.out.println(
                    "Maximum focus violations reached. Quiz should be submitted."
            );

            finishQuiz();
        }
    }

    public void finishQuiz() {
        if (quizStage == null) {
            return;
        }

        QuizLockService.unlockQuizWindow(quizStage);

        System.out.println(
                "Quiz finished with "
                        + QuizLockService.getFocusViolations()
                        + " focus violations."
        );
    }

    public boolean isQuizLocked() {
        return QuizLockService.isQuizLocked();
    }
}