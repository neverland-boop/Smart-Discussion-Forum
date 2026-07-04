package config;

import java.io.File;

/**
 * Central configuration for the embedded SQLite database used by the
 * Java Desktop Client (Bidal — Desktop Core & Security).
 *
 * Sprint 1 scope: define where the local database lives and expose the
 * JDBC URL used to open it. Table/schema creation and connection handling
 * live in {@link utils.DatabaseConnection}.
 */
public class DatabaseConfig {

    /** Folder (inside the user's home directory) that holds all local app data. */
    private static final String APP_DATA_DIR =
            System.getProperty("user.home") + File.separator + ".smart_discussion_forum";

    /** Name of the embedded SQLite database file. */
    private static final String DB_FILE_NAME = "smart-forum.db";

    private DatabaseConfig() {
        // Utility class — no instances.
    }

    /**
     * Returns the absolute path to the SQLite database file, creating the
     * containing directory if it does not already exist.
     *
     * NOTE: SQLite will NOT create missing parent directories on its own —
     * if APP_DATA_DIR doesn't exist yet, DriverManager.getConnection() fails
     * silently or throws. We guard against that here.
     */
    public static String getDatabaseFilePath() {
        File dir = new File(APP_DATA_DIR);

        if (!dir.exists()) {
            dir.mkdirs();
        }

        return APP_DATA_DIR + File.separator + DB_FILE_NAME;
    }

    /** JDBC connection URL for the embedded SQLite database. */
    public static String getJdbcUrl() {
        return "jdbc:sqlite:" + getDatabaseFilePath();
    }
}
