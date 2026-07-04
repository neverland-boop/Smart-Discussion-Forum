package dto;

import org.json.JSONObject;

/**
 * Mirrors what Laravel/Breeze's default registration validation expects:
 * name, email, password, password_confirmation.
 *
 * NOTE: an earlier draft sent "confirmPassword" as the field name. Laravel's
 * built-in `confirmed` validation rule specifically looks for
 * `password_confirmation` — a mismatched key would fail validation on
 * Duncan's side even with correct values. Fixed here.
 */
public class RegisterRequest {

    public final String name;
    public final String email;
    public final String password;
    public final String passwordConfirmation;

    public RegisterRequest(String name, String email, String password, String passwordConfirmation) {
        this.name = name;
        this.email = email;
        this.password = password;
        this.passwordConfirmation = passwordConfirmation;
    }

    public String toJson() {
        JSONObject obj = new JSONObject();
        obj.put("name", name);
        obj.put("email", email);
        obj.put("password", password);
        obj.put("password_confirmation", passwordConfirmation);
        return obj.toString();
    }
}
