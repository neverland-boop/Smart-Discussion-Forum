package controller;

import javafx.animation.KeyFrame;
import javafx.animation.Timeline;
import javafx.application.Platform;
import javafx.fxml.FXML;
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
     * The backend will provide this during group integration.
     */
    private int attemptId;

    private int currentQuestionIndex = 0;
    private int remainingSeconds = 10 * 60;

    private boolean submitted = false;
    private boolean submissionInProgress = false;

    /*
     * Temporary questions for testing the completed frontend.
     * These will later be replaced by questions received from Laravel.
     */
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

    /*
     * Stores answers using:
     * question number -> selected option
     *
     * Example:
     * 1 -> A
     * 2 -> C
     */
    private final Map<Integer, String> selectedAnswers =
            new LinkedHashMap<>();

    @FXML
    private void initialize() {
        configureAnswerOptions();
        displayCurrentQuestion();
        updateTimerLabel();
        updateViolationLabel();
    }

    private void configureAnswerOptions() {
        optionA.setUserData("A");
        optionB.setUserData("B");
        optionC.setUserData("C");
        optionD.setUserData("D");
    }

    /*
     * Called by the screen that opens the quiz.
     */
    public void startQuiz(Stage stage) {
        if (stage == null) {
            System.out.println("Quiz stage was not provided.");
            return;
        }

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

    /*
     * The real attempt ID will be passed here when the backend
     * and frontend are connected during group testing.
     */
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

                    updateTimerLabel();
                    updateViolationLabel();

                    if (remainingSeconds <= 0) {
                        remainingSeconds = 0;
                        timer.stop();
                        autoSubmitForTime();
                    }
                })
        );

        timer.setCycleCount(Timeline.INDEFINITE);
        timer.play();
    }

    private void updateTimerLabel() {
        if (timerLabel == null) {
            return;
        }

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
            System.out.println(
                    "Please select an answer before continuing."
            );
            return false;
        }

        int questionNumber = currentQuestionIndex + 1;
        String selectedOption =
                String.valueOf(selectedToggle.getUserData());

        selectedAnswers.put(
                questionNumber,
                selectedOption
        );

        System.out.println(
                "Saved answer for question "
                        + questionNumber
                        + ": "
                        + selectedOption
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
            System.out.println(
                    "All questions have been answered."
            );
            submitQuiz();
        }
    }

    @FXML
    private void submitQuiz() {
        if (submitted || submissionInProgress) {
            return;
        }

        /*
         * Save the current selection when one exists.
         * A user may press Submit directly instead of Next.
         */
        if (answerGroup.getSelectedToggle() != null) {
            saveCurrentAnswer();
        }

        if (selectedAnswers.size()
                < questionTexts.length) {

            System.out.println(
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
                "Quiz automatically submitted because the focus-violation limit was reached."
        );
    }

    private void autoSubmitForTime() {
        /*
         * Preserve the answer currently selected when time expires.
         */
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

        if (attemptId <= 0) {
            String preparedJson = buildAnswersJson();

            System.out.println(submissionMessage);
            System.out.println(
                    "Answers prepared successfully: "
                            + preparedJson
            );
            System.out.println(
                    "Backend submission is waiting for the real attempt ID during group integration."
            );

            /*
             * The frontend work is complete, but do not send to an
             * invented backend attempt.
             */
            return;
        }

        submissionInProgress = true;

        if (timer != null) {
            timer.pause();
        }

        setControlsDisabled(true);

        String answersJson = buildAnswersJson();

        System.out.println("Submitting quiz...");
        System.out.println("Answers: " + answersJson);

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

            System.out.println(submissionMessage);
            System.out.println(
                    "Server response: " + response.body
            );

            finishQuiz();
            return;
        }

        /*
         * SyncService queues POST requests when the server
         * cannot be reached.
         */
        if (response.statusCode == -1) {
            submitted = true;

            System.out.println(
                    "The server is unavailable."
            );
            System.out.println(
                    "The quiz submission was placed in the offline queue."
            );
            System.out.println(response.body);

            finishQuiz();
            return;
        }

        System.out.println(
                "Quiz submission failed. Status: "
                        + response.statusCode
        );
        System.out.println(response.body);

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
        if (violationLabel == null) {
            return;
        }

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