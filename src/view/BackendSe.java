package view;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

public class BackendSe {

    // Ask your colleague for this exact URL endpoint!
    private static final String LARAVEL_API_URL = "http://localhost:8000/api/register";

    public static boolean saveUserToDatabase(String fullName, String email, String password, String confirmPassword) {
        try {
            // 1. Create a JSON string to match what Laravel expects
            String jsonPayload = String.format(
                    "{\"name\":\"%s\", \"email\":\"%s\", \"password\":\"%s\", \"confirmPassword\":\"%s\"}",
                    fullName, email, password, confirmPassword
            );

            // 2. Build the HTTP POST request
            HttpClient client = HttpClient.newHttpClient();
            HttpRequest request = HttpRequest.newBuilder()
                    .uri(URI.create(LARAVEL_API_URL))
                    .header("Content-Type", "application/json")
                    .header("Accept", "application/json")
                    .POST(HttpRequest.BodyPublishers.ofString(jsonPayload))
                    .build();

            // 3. Send the request and check Laravel's response
            HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());

            // If Laravel returns 200 (OK) or 201 (Created), it worked!
            if (response.statusCode() == 200 || response.statusCode() == 201) {
                System.out.println("Laravel Response: " + response.body());
                return true;
            } else {
                System.err.println("Failed to register. Server status: " + response.statusCode());
                System.err.println("Server error message: " + response.body());
                return false;
            }


        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }

}
