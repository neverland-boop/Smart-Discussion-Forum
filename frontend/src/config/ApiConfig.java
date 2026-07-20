package config;

/**
 * Central configuration for reaching the Laravel backend.
 *
 * IMPORTANT — Sanctum, not raw JWT: the backend issues Sanctum "personal
 * access tokens" (opaque strings from $user->createToken()->plainTextToken),
 * not JWTs. The desktop client never decodes the token — it just stores
 * whatever string comes back and replays it as:
 *
 *     Authorization: Bearer <token>
 *
 * on every subsequent authenticated request. The web client (Livewire /
 * Breeze) instead relies on session cookies + CSRF, which is Francis's
 * concern and unaffected by anything here.
 */
public class ApiConfig {

    /** Update to match wherever `php artisan serve` (or production) is running. */
    public static final String BASE_URL = "http://localhost:8000/api";

    public static final String REGISTER_ENDPOINT = BASE_URL + "/register";
    public static final String LOGIN_ENDPOINT = BASE_URL + "/login";
    public static final String LOGOUT_ENDPOINT = BASE_URL + "/logout";


    private ApiConfig() {
        // Utility class — no instances.
    }
}
