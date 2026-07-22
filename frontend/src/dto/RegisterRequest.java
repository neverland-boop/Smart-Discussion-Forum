package dto;
import org.json.JSONObject;

public class RegisterRequest {

    public final String name;
    public final String email;
    public final String password;
    public final String passwordConfirmation;

    public RegisterRequest(
            String name,
            String email,
            String password,
            String passwordConfirmation
    ) {
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
        obj.put("agreed_to_rules", true);

        return obj.toString();
    }
}