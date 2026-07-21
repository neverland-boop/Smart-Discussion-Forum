package service;

import storage.TokenStorage;
import utils.ApiClient;

public class QuizService {

    private static final String BASE_URL = "http://127.0.0.1:8000/api";

    private QuizService() {
    }

    public static ApiClient.ApiResponse getAvailableQuizzes() {
        String token = TokenStorage.getToken();

        return SyncService.sendGet(
                BASE_URL + "/quizzes",
                token
        );
    }

    public static ApiClient.ApiResponse getQuizById(int quizId) {
        String token = TokenStorage.getToken();

        return SyncService.sendGet(
                BASE_URL + "/quizzes/" + quizId,
                token
        );
    }

    public static ApiClient.ApiResponse submitAttempt(
            int attemptId,
            String answersJson,
            boolean autoSubmitted
    ) {
        String token = TokenStorage.getToken();

        String payload = String.format(
                "{\"answers\":%s,\"auto_submitted\":%s}",
                answersJson,
                autoSubmitted
        );

        return SyncService.sendPost(
                BASE_URL + "/attempts/" + attemptId + "/submit",
                payload,
                token
        );
    }
