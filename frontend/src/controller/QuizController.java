package controller;

import javafx.animation.KeyFrame;
import javafx.animation.Timeline;
import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.RadioButton;
import javafx.scene.control.Toggle;
import javafx.scene.control.ToggleGroup;
import javafx.stage.Stage;
import javafx.util.Duration;
import service.QuizLockService;
import service.QuizService;
import utils.ApiClient;

import java.util.LinkedHashMap;
import java.util.Map;

public class QuizController {

    @FXML
    private Label timerLabel;

    @FXML
    private Label violationLabel;

    @FXML
    private Label questionNumberLabel;

    @FXML
    private Label questionTextLabel;

    @FXML
    private RadioButton optionA;

    @FXML
    private RadioButton optionB;

    @FXML
    private RadioButton optionC;

    @FXML
    private RadioButton optionD;

    @FXML
    private ToggleGroup answerGroup;

    @FXML
    private Button nextButton;

    private Stage quizStage;
    private Timeline timer;

    /*
     * This must eventually come from Duncan's backend.
     */
    private int attemptId = 0;

    private int currentQuestionIndex = 0;
    private int remainingSeconds = 10 * 60;

    private boolean submitted = false;
    private boolean submissionInProgress = false;
    private boolean quizStarted = false;

    private final String[] questionTexts = {
            "Which language is mainly used to build JavaFX applications?",
            "Which file type is commonly used to design a JavaFX interface?",
            "Which service handles the quiz window focus restrictions?"
    };

    private final String[][] questionOptions = {
            {"Java", "PHP", "Python", "C"},
            {"FXML", "SQL", "JSON", "CSV"},
            {
                    "QuizLockService",
                    "ChatService",
                    "AuthService",
                    "NotificationService"
            }
    };

    private final Map<Integer, String> selectedAnswers =
            new LinkedHashMap<>();

    @FXML
    private void initialize() {
        configureAnswerOptions();
        displayCurrentQuestion();
        updateTimerLabel();
        updateViolationLabel();

        /*
         * Wait until the FXML has been placed inside a window,
         * then obtain the Stage and start the quiz automatically.
         */
        Platform.runLater(() -> {
            if (timerLabel.getScene() == null) {
                showMessage("The quiz window could not be detected.");
                return;
            }

            quizStage =
                    (Stage) timerLabel.getScene().getWindow();

            startQuiz(quizStage);
        });
    }

    private void configureAnswerOptions() {
        optionA.setUserData("A");
        optionB.setUserData("B");
        optionC.setUserData("C");
        optionD.setUserData("D");
    }

    public void startQuiz(Stage stage) {
        if (quizStarted) {
            return;
        }

        if (stage == null) {
            showMessage("The quiz window was not provided.");
            return;
        }

        quizStarted = true;
        quizStage = stage;

        QuizLockService.lockQuizWindow(
                quizStage,
                this::autoSubmitForViolations
        );

        startTimer();
        updateViolationLabel();

        System.out.println("Quiz started.");
        System.out.println("Quiz lock activated.");
    }

    public void setAttemptId(int attemptId) {
        if (attemptId <= 0) {
            System.out.println("Invalid quiz attempt ID.");
            return;
        }

        this.attemptId = attemptId;

        System.out.println(
                "Quiz attempt ID received: " + attemptId
        );
    }

    private void startTimer() {
        if (timer != null) {
            timer.stop();
        }

        timer = new Timeline(
                new KeyFrame(Duration.seconds(1), event -> {

                    if (submitted || submissionInProgress) {
                        return;
                    }

                    remainingSeconds--;

                    if (remainingSeconds < 0) {
                        remainingSeconds = 0;
                    }

                    updateTimerLabel();
                    updateViolationLabel();

                    if (remainingSeconds == 0) {
                        timer.stop();
                        autoSubmitForTime();
                    }
                })
        );

        timer.setCycleCount(Timeline.INDEFINITE);
        timer.playFromStart();
    }

    private void updateTimerLabel() {
        int minutes = remainingSeconds / 60;
        int seconds = remainingSeconds % 60;

        timerLabel.setText(
                String.format(
                        "Time: %02d:%02d",
                        minutes,
                        seconds
                )
        );
    }

    private void displayCurrentQuestion() {
        if (currentQuestionIndex < 0
                || currentQuestionIndex >= questionTexts.length) {
            return;
        }

        int displayedNumber = currentQuestionIndex + 1;

        questionNumberLabel.setText(
                "Question "
                        + displayedNumber
                        + " of "
                        + questionTexts.length
        );

        questionTextLabel.setText(
                questionTexts[currentQuestionIndex]
        );

        String[] options =
                questionOptions[currentQuestionIndex];

        optionA.setText(options[0]);
        optionB.setText(options[1]);
        optionC.setText(options[2]);
        optionD.setText(options[3]);

        restoreSavedAnswer();

        if (currentQuestionIndex
                == questionTexts.length - 1) {
            nextButton.setText("Finish Questions");
        } else {
            nextButton.setText("Next Question");
        }
    }

    private void restoreSavedAnswer() {
        answerGroup.selectToggle(null);

        int questionNumber = currentQuestionIndex + 1;
        String savedAnswer =
                selectedAnswers.get(questionNumber);

        if (savedAnswer == null) {
            return;
        }

        for (Toggle toggle : answerGroup.getToggles()) {
            if (savedAnswer.equals(
                    String.valueOf(toggle.getUserData())
            )) {
                answerGroup.selectToggle(toggle);
                break;
            }
        }
    }

    private boolean saveCurrentAnswer() {
        Toggle selectedToggle =
                answerGroup.getSelectedToggle();

        if (selectedToggle == null) {
            showMessage(
                    "Please select an answer before continuing."
            );
            return false;
        }

        int questionNumber = currentQuestionIndex + 1;

        String selectedOption =
                String.valueOf(
                        selectedToggle.getUserData()
                );

        selectedAnswers.put(
                questionNumber,
                selectedOption
        );

        return true;
    }

    @FXML
    private void nextQuestion() {
        if (submitted || submissionInProgress) {
            return;
        }

        if (!saveCurrentAnswer()) {
            return;
        }

        if (currentQuestionIndex
                < questionTexts.length - 1) {

            currentQuestionIndex++;
            displayCurrentQuestion();

        } else {
            submitQuiz();
        }
    }

    @FXML
    private void submitQuiz() {
        if (submitted || submissionInProgress) {
            return;
        }

        if (answerGroup.getSelectedToggle() != null) {
            saveCurrentAnswer();
        }

        if (selectedAnswers.size()
                < questionTexts.length) {

            showMessage(
                    "Please answer every question before submitting."
            );
            return;
        }

        submitQuizToServer(
                false,
                "Quiz manually submitted."
        );
    }

    private void autoSubmitForViolations() {
        submitQuizToServer(
                true,
                "Quiz automatically submitted because the violation limit was reached."
        );
    }

    private void autoSubmitForTime() {
        if (answerGroup.getSelectedToggle() != null) {
            saveCurrentAnswer();
        }

        submitQuizToServer(
                true,
                "Quiz automatically submitted because time expired."
        );
    }

    private void submitQuizToServer(
            boolean autoSubmitted,
            String submissionMessage
    ) {
        if (submitted || submissionInProgress) {
            return;
        }

        String answersJson = buildAnswersJson();

        /*
         * The quiz can run locally, but real server submission
         * needs an attempt ID supplied by the backend.
         */
        if (attemptId <= 0) {
            System.out.println(submissionMessage);
            System.out.println(
                    "Prepared answers: " + answersJson
            );

            showMessage(
                    "Your answers were prepared successfully, "
                            + "but they cannot yet be sent because "
                            + "the backend did not provide a quiz attempt ID."
            );
            return;
        }

        submissionInProgress = true;

        if (timer != null) {
            timer.pause();
        }

        setControlsDisabled(true);

        Thread submissionThread = new Thread(() -> {

            ApiClient.ApiResponse response =
                    QuizService.submitAttempt(
                            attemptId,
                            answersJson,
                            autoSubmitted
                    );

            Platform.runLater(() ->
                    handleSubmissionResponse(
                            response,
                            submissionMessage
                    )
            );
        });

        submissionThread.setDaemon(true);
        submissionThread.start();
    }

    private String buildAnswersJson() {
        StringBuilder json =
                new StringBuilder("{");

        int position = 0;

        for (Map.Entry<Integer, String> answer
                : selectedAnswers.entrySet()) {

            if (position > 0) {
                json.append(",");
            }

            json.append("\"")
                    .append(answer.getKey())
                    .append("\":\"")
                    .append(escapeJson(answer.getValue()))
                    .append("\"");

            position++;
        }

        json.append("}");

        return json.toString();
    }

    private String escapeJson(String value) {
        if (value == null) {
            return "";
        }

        return value
                .replace("\\", "\\\\")
                .replace("\"", "\\\"");
    }

    private void handleSubmissionResponse(
            ApiClient.ApiResponse response,
            String submissionMessage
    ) {
        submissionInProgress = false;

        if (response.isSuccess()) {
            submitted = true;

            showMessage(submissionMessage);

            System.out.println(
                    "Server response: " + response.body
            );

            finishQuiz();
            return;
        }

        if (response.statusCode == -1) {
            submitted = true;

            showMessage(
                    "The server is unavailable. "
                            + "Your submission was placed in the offline queue."
            );

            finishQuiz();
            return;
        }

        showMessage(
                "Quiz submission failed. HTTP "
                        + response.statusCode
                        + "\n"
                        + response.body
        );

        setControlsDisabled(false);

        if (timer != null && remainingSeconds > 0) {
            timer.play();
        }
    }

    private void setControlsDisabled(boolean disabled) {
        optionA.setDisable(disabled);
        optionB.setDisable(disabled);
        optionC.setDisable(disabled);
        optionD.setDisable(disabled);
        nextButton.setDisable(disabled);
    }

    private void updateViolationLabel() {
        violationLabel.setText(
                "Violations: "
                        + QuizLockService.getFocusViolations()
                        + "/"
                        + QuizLockService.getMaxViolations()
        );
    }

    public void finishQuiz() {
        if (timer != null) {
            timer.stop();
        }

        if (quizStage != null) {
            QuizLockService.unlockQuizWindow(quizStage);
        }

        System.out.println(
                "Quiz finished with "
                        + QuizLockService.getFocusViolations()
                        + " focus violations."
        );
    }

    public boolean isQuizLocked() {
        return QuizLockService.isQuizLocked();
    }

    private void showMessage(String message) {
        Alert alert =
                new Alert(Alert.AlertType.INFORMATION);

        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }
}