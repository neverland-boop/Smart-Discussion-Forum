package config;

/**
 * Central configuration for reaching the Laravel backend.
 *
 * IMPORTANT — Sanctum, not raw JWT: the backend issues Sanctum personal
 * access tokens. The desktop client stores the token and sends it as:
 *
 * Authorization: Bearer <token>
 */
public class ApiConfig {

    /** Production backend (Duncan's Render server). */
    public static final String BASE_URL =
            "https://smart-discussion-forum-backend.onrender.com/api";

    public static final String REGISTER_ENDPOINT = BASE_URL + "/register";
    public static final String LOGIN_ENDPOINT = BASE_URL + "/login";
    public static final String LOGOUT_ENDPOINT = BASE_URL + "/logout";


    private ApiConfig() {
        // Utility class — no instances.
    }
}