package dto;

import org.json.JSONObject;

public class LoginResponse {

    public boolean success;
    public String token;
    public String userEmail;
    public String userName;
    public String role;
    public String message;

    public static LoginResponse fromJson(int statusCode, String body) {
        LoginResponse result = new LoginResponse();

        try {
            JSONObject obj = new JSONObject(body);

            if (statusCode >= 200 && statusCode < 300) {
                result.success = true;

                result.token = obj.optString(
                        "token",
                        obj.optString(
                                "access_token",
                                obj.optString("plainTextToken", null)
                        )
                );

                JSONObject user = obj.optJSONObject("user");

                if (user != null) {
                    result.userEmail = user.optString("email", null);
                    result.userName = user.optString("name", null);
                    result.role = user.optString("role", null);
                }

            } else {
                result.success = false;
                result.message = obj.optString(
                        "message",
                        "Request failed (HTTP " + statusCode + ")"
                );
            }

        } catch (Exception exception) {
            result.success = false;

            result.message = (statusCode == -1)
                    ? "Could not reach the server."
                    : "Could not parse server response.";
        }

        return result;
    }
}