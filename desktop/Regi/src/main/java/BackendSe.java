
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.util.prefs.Preferences;

public class BackendSe {

    // Replace with your partner's actual Laravel IP address and endpoint
    private static final String BACKEND_URL = "http://192.168.1.8/api/register";

    public static void sendRegistration(String name, String email, String password) {
        try {
            HttpClient client = HttpClient.newHttpClient();

            // Create JSON keys matching what your partner's Laravel expects
            String jsonPayload = String.format(
                    "{\"username\":\"%s\", \"email\":\"%s\",\"password\":\"%s\", \"password-confirmation\":\"%s\"}",
                    name, email, password
            );

            HttpRequest request = HttpRequest.newBuilder()
                    .uri(URI.create(BACKEND_URL))
                    .header("Content-Type", "application/json")
                    .header("Accept", "application/json")
                    .POST(HttpRequest.BodyPublishers.ofString(jsonPayload))
                    .build();

            client.sendAsync(request, HttpResponse.BodyHandlers.ofString())
                    .thenAccept(response -> {
                        System.out.println("Laravel Status Code: " + response.statusCode());
                        System.out.println("Laravel Response: " + response.body());

                        if (response.statusCode() == 200 || response.statusCode() == 201) {
                            // Extract and save the token if successful
                            saveTokenLocally("extracted_token_string_here");
                        }
                    });

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private static void saveTokenLocally(String token) {
        Preferences userPrefs = Preferences.userRoot().node("com.smartforum.app");
        userPrefs.put("auth_token", token);
        System.out.println("Token saved successfully in local preferences storage!");
    }
}
