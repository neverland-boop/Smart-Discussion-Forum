package model;

/**
 * Local, lightweight mirror of the central Users table fields relevant to
 * the desktop client (SDD Section 4.2 Data Dictionary). Used both when
 * caching identity locally in SQLite (Bidal) and when displaying the
 * logged-in user in the UI (Patience/Anthony).
 */
public class User {

    private int id;
    private String name;
    private String email;
    private String role;
    private String accountStatus;

    public User(int id, String name, String email, String role, String accountStatus) {
        this.id = id;
        this.name = name;
        this.email = email;
        this.role = role;
        this.accountStatus = accountStatus;
    }

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getRole() { return role; }
    public void setRole(String role) { this.role = role; }

    public String getAccountStatus() { return accountStatus; }
    public void setAccountStatus(String accountStatus) { this.accountStatus = accountStatus; }
}
