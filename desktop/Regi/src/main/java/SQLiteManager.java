import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;

public class SQLiteManager {

    private static final String DB_URL = "jdbc:sqlite:smart_forum.db";

    /**
     * Creates the SQLite database and required tables.
     */
    public static void initializeDatabase() {

        try (Connection conn = DriverManager.getConnection(DB_URL)) {

            if (conn != null) {
                System.out.println("SQLite connected successfully!");

                Statement stmt = conn.createStatement();

                stmt.execute("""
                        CREATE TABLE IF NOT EXISTS users (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            username TEXT NOT NULL,
                            email TEXT NOT NULL,
                            password TEXT NOT NULL,
                            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                        );
                        """);

                stmt.close();

                System.out.println("Users table created successfully.");

            }

        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}