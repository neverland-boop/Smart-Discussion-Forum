package storage;

import utils.DatabaseConnection;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

public class OfflineStorage {

    private OfflineStorage() {
    }

    public static void savePendingRequest(
            String endpoint,
            String requestMethod,
            String payload
    ) {
        String sql = """
                INSERT INTO offline_queue (
                    endpoint,
                    request_method,
                    payload,
                    status
                )
                VALUES (?, ?, ?, 'PENDING')
                """;

        try (Connection connection = DatabaseConnection.getConnection();
             PreparedStatement statement = connection.prepareStatement(sql)) {

            statement.setString(1, endpoint);
            statement.setString(2, requestMethod);
            statement.setString(3, payload);

            statement.executeUpdate();

            System.out.println("Request saved to offline queue.");

        } catch (SQLException exception) {
            System.err.println(
                    "Could not save pending request: "
                            + exception.getMessage()
            );
        }
    }

    public static ResultSet getPendingRequests(Connection connection)
            throws SQLException {

        String sql = """
                SELECT *
                FROM offline_queue
                WHERE status = 'PENDING'
                ORDER BY id ASC
                """;

        PreparedStatement statement = connection.prepareStatement(sql);
        return statement.executeQuery();
    }

    public static void markAsSent(Connection connection, int id)
            throws SQLException {

        String sql = """
                UPDATE offline_queue
                SET status = 'SENT'
                WHERE id = ?
                """;

        try (PreparedStatement statement =
                     connection.prepareStatement(sql)) {

            statement.setInt(1, id);
            statement.executeUpdate();
        }
    }

    public static void increaseRetryCount(Connection connection, int id)
            throws SQLException {

        String sql = """
                UPDATE offline_queue
                SET retry_count = retry_count + 1
                WHERE id = ?
                """;

        try (PreparedStatement statement =
                     connection.prepareStatement(sql)) {

            statement.setInt(1, id);
            statement.executeUpdate();
        }
    }
}