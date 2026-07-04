package utils;

import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;

/**
 * Thin HTTP wrapper — this is "The Controller" described in the SDD's
 * Java-Laravel Bridge Management section (5. The Java-Laravel Bridge
 * Management): it owns the single HttpClient, injects the Sanctum bearer
 * token when one is available, and hands back a plain ApiResponse so
 * nothing else in the app touches java.net.http directly.
 *
 * Per the SDD, Patience's screens must NOT build HTTP requests themselves —
 * they call service.AuthService, which calls this.
 */
public class ApiClient {

    private static final HttpClient CLIENT = HttpClient.newBuilder()
            .connectTimeout(Duration.ofSeconds(10))
            .build();

    public static class ApiResponse {
        public final int statusCode;
        public final String body;

        public ApiResponse(int statusCode, String body) {
            this.statusCode = statusCode;
            this.body = body;
        }

        public boolean isSuccess() {
            return statusCode >= 200 && statusCode < 300;
        }
    }

    public static ApiResponse post(String url, String jsonBody, String bearerToken) {
        try {
            HttpRequest.Builder builder = HttpRequest.newBuilder()
                    .uri(URI.create(url))
                    .header("Content-Type", "application/json")
                    .header("Accept", "application/json")
                    .POST(HttpRequest.BodyPublishers.ofString(jsonBody));

            if (bearerToken != null && !bearerToken.isBlank()) {
                builder.header("Authorization", "Bearer " + bearerToken);
            }

            HttpResponse<String> response = CLIENT.send(builder.build(), HttpResponse.BodyHandlers.ofString());
            return new ApiResponse(response.statusCode(), response.body());

        } catch (Exception e) {
            return new ApiResponse(-1, "{\"message\":\"Network error: " + e.getMessage() + "\"}");
        }
    }

    public static ApiResponse get(String url, String bearerToken) {
        try {
            HttpRequest.Builder builder = HttpRequest.newBuilder()
                    .uri(URI.create(url))
                    .header("Accept", "application/json")
                    .GET();

            if (bearerToken != null && !bearerToken.isBlank()) {
                builder.header("Authorization", "Bearer " + bearerToken);
            }

            HttpResponse<String> response = CLIENT.send(builder.build(), HttpResponse.BodyHandlers.ofString());
            return new ApiResponse(response.statusCode(), response.body());

        } catch (Exception e) {
            return new ApiResponse(-1, "{\"message\":\"Network error: " + e.getMessage() + "\"}");
        }
    }
}
