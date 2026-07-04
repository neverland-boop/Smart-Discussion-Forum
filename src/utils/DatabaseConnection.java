package utils;

import java.io.File;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;

public class DatabaseConnection {

    // 1. Define the portable, relative path for the database
    private static final String DB_FOLDER = "data";
    private static final String DB_URL = "jdbc:sqlite:" + DB_FOLDER + "/smart-forum.db";

    /**
     * Establishes a connection to the SQLite database.
     * It ensures the target folder exists before attempting to connect.
     */
    public static Connection getConnection() throws SQLException {
        // Create the directory if it doesn't exist
        File directory = new File(DB_FOLDER);
        if (!directory.exists()) {
            directory.mkdir();
        }

        // Return the active connection
        return DriverManager.getConnection(DB_URL);
    }

    /**
     * Initializes the database schema.
     * Called exactly once when the Launcher starts.
     */
    public static void initializeDatabase() {
        // The SQL schema using Java 15+ text blocks
        String sql = """
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    password_hash TEXT NOT NULL,
                    account_status TEXT NOT NULL DEFAULT 'PENDING',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
                """;

        // 2. Use try-with-resources to automatically close the connection and statement
        try (Connection conn = getConnection();
             Statement stmt = conn.createStatement()) {

            // Execute the table creation
            stmt.execute(sql);

            // Log success with the new relative path
            System.out.println("SQLite connected successfully at ./" + DB_FOLDER + "/smart-forum.db");
            System.out.println("users table ready.");

        } catch (SQLException e) {
            System.err.println("Could not initialize local SQLite database: " + e.getMessage());
        }
    }
}