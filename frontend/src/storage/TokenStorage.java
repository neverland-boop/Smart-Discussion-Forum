package storage;

import java.io.*;

/**
 * Single source of truth for persisting the desktop client's auth session.
 *
 * An earlier draft had TWO competing implementations: this file-based class
 * (used by LoginScreen) and java.util.prefs.Preferences (used by Reg) —
 * meaning a token saved by one screen was invisible to the other. There is
 * now only one mechanism, and every screen (Reg, LoginController) goes
 * through it.
 *
 * NOTE: the value saved here is whatever token Sanctum returned from
 * /api/login or /api/register — never a locally generated UUID.
 */
public class TokenStorage {

    private static final String TOKEN_FILE =
            System.getProperty("user.home") + File.separator + ".smart_forum_token";

    private static final String EMAIL_FILE =
            System.getProperty("user.home") + File.separator + ".smart_forum_user";

    public static void saveToken(String token) {
        writeFile(TOKEN_FILE, token);
    }

    public static String getToken() {
        return readFile(TOKEN_FILE);
    }

    public static void saveLoggedInEmail(String email) {
        writeFile(EMAIL_FILE, email);
    }

    public static String getLoggedInEmail() {
        return readFile(EMAIL_FILE);
    }

    /** Clears the whole local session (used on logout, and by Launcher for a clean dev start). */
    public static void clearToken() {
        deleteFile(TOKEN_FILE);
        deleteFile(EMAIL_FILE);
    }

    private static void writeFile(String path, String value) {
        try (BufferedWriter writer = new BufferedWriter(new FileWriter(path))) {
            writer.write(value == null ? "" : value);
        } catch (IOException e) {
            System.out.println("Could not save to " + path);
        }
    }

    private static String readFile(String path) {
        File file = new File(path);
        if (!file.exists()) {
            return null;
        }

        try (BufferedReader reader = new BufferedReader(new FileReader(path))) {
            String line = reader.readLine();
            return (line == null || line.isBlank()) ? null : line;
        } catch (IOException e) {
            return null;
        }
    }

    private static void deleteFile(String path) {
        File file = new File(path);
        if (file.exists()) {
            file.delete();
        }
    }
}
