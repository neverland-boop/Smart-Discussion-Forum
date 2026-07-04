package model;

/**
 * Represents the desktop client's current authenticated session: the
 * Sanctum bearer token plus the user it belongs to. Kept separate from
 * User so "am I logged in" checks don't require a full user record.
 */
public class Session {

    private final String token;
    private final User user;

    public Session(String token, User user) {
        this.token = token;
        this.user = user;
    }

    public String getToken() { return token; }
    public User getUser() { return user; }

    public boolean isActive() {
        return token != null && !token.isBlank();
    }
}
