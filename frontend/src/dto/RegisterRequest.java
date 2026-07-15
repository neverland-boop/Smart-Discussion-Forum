package dto;

import org.json.JSONObject;

/**
 * Mirrors what Laravel/Breeze's default registration validation expects:
 * name, email, password, password_confirmation, agreed_to_rules.
 */
public class RegisterRequest {

    public final String name;
    public final String email;
    public final String password;
    public final String passwordConfirmation;
    // 1. ADD THE FIELD HERE
    public final boolean agreedToRules;

    // 2. UPDATE THE CONSTRUCTOR TO ACCEPT THE NEW PARAMETER
    public RegisterRequest(String name, String email, String password, String passwordConfirmation, boolean agreedToRules) {
        this.name = name;
        this.email = email;
        this.password = password;
        this.passwordConfirmation = passwordConfirmation;
        this.agreedToRules = agreedToRules;
    }

    public String toJson() {
        JSONObject obj = new JSONObject();
        obj.put("name", name);
        obj.put("email", email);
        obj.put("password", password);
        obj.put("password_confirmation", passwordConfirmation);
        // 3. ADD IT TO THE JSON OBJECT HERE
        obj.put("agreed_to_rules", agreedToRules);

        return obj.toString();
    }
}