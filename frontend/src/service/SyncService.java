package service;

import storage.OfflineStorage;
import storage.TokenStorage;
import utils.ApiClient;
import utils.DatabaseConnection;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;

public class SyncService {

    private SyncService() {
    }

    public static ApiClient.ApiResponse sendPost(
            String endpoint,
            String payload,
            String bearerToken
    ) {
        ApiClient.ApiResponse response =
                ApiClient.post(endpoint, payload, bearerToken);

        if (response.statusCode == -1) {
            OfflineStorage.savePendingRequest(
                    endpoint,
                    "POST",
                    payload
            );

            System.out.println(
                    "Request queued because the server is unavailable."
            );
        }

        return response;
    }

    public static ApiClient.ApiResponse sendGet(
            String endpoint,
            String bearerToken
    ) {
        return ApiClient.get(endpoint, bearerToken);
    }

    public static void retryPendingRequests() {

        try (Connection connection =
                     DatabaseConnection.getConnection();
             ResultSet resultSet =
                     OfflineStorage.getPendingRequests(connection)) {

            while (resultSet.next()) {

                int id = resultSet.getInt("id");
                String endpoint =
                        resultSet.getString("endpoint");
                String requestMethod =
                        resultSet.getString("request_method");
                String payload =
                        resultSet.getString("payload");

                String token = TokenStorage.getToken();

                ApiClient.ApiResponse response;

                if ("POST".equalsIgnoreCase(requestMethod)) {
                    response = ApiClient.post(
                            endpoint,
                            payload,
                            token
                    );
                } else if ("GET".equalsIgnoreCase(requestMethod)) {
                    response = ApiClient.get(
                            endpoint,
                            token
                    );
                } else {
                    OfflineStorage.increaseRetryCount(
                            connection,
                            id
                    );

                    System.out.println(
                            "Unsupported request method: "
                                    + requestMethod
                    );
                    continue;
                }

                if (response.isSuccess()) {
                    OfflineStorage.markAsSent(
                            connection,
                            id
                    );

                    System.out.println(
                            "Queued request synced successfully."
                    );
                } else {
                    OfflineStorage.increaseRetryCount(
                            connection,
                            id
                    );

                    System.out.println(
                            "Queued request failed again."
                    );
                }
            }

        } catch (SQLException exception) {
            System.err.println(
                    "Error syncing offline requests: "
                            + exception.getMessage()
            );
        }
    }
}