package view;

public enum UserRole {
    STUDENT, LECTURER, ADMIN
}

 class UserSession {
    private String username;
    private UserRole role;

    public UserSession(String username, UserRole role) {
        this.username = username;
        this.role = role;
    }

    public UserRole getRole() { return role; }
    public String getUsername() { return username; }
}