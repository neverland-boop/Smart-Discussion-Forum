package dto;

import org.json.JSONObject;

/**
 * Parses whatever shape Duncan's Sanctum-backed /api/login and
 * /api/register controllers return. Expected contract (confirm exact
 * field names with Duncan before Day 3 wire-up):
 *
 *   { "user": { "id": 1, "name": "...", "email": "...", "role": "..." },
 *     "token": "<sanctum plain-text token>" }
 *
 * Sanctum tokens are opaque strings, not JWTs — there is nothing to decode
 * client-side. They are only ever stored and replayed as a bearer token.
 */
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
                result.token = obj.optString("token", null);

                JSONObject user = obj.optJSONObject("user");
                if (user != null) {
                    result.userEmail = user.optString("email", null);
                    result.userName = user.optString("name", null);
                    result.role = user.optString("role", null);
                }
            } else {
                result.success = false;
                // Laravel validation errors normally arrive as
                // {"message": "...", "errors": {...}} — surface the message.
                result.message = obj.optString("message", "Request failed (HTTP " + statusCode + ")");
            }

        } catch (Exception e) {
            result.success = false;
            result.message = (statusCode == -1)
                    ? "Could not reach the server. Is it running?"
                    : "Could not parse server response.";
        }

        return result;
    }
}
