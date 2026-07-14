package service;

import model.Message;
import storage.TokenStorage;
import utils.ApiClient;

public class ChatService {

    private ChatService() {
    }

    public static ApiClient.ApiResponse sendMessage(
            String endpoint,
            Message message
    ) {
        String payload = createMessageJson(message);
        String token = TokenStorage.getToken();

        return SyncService.sendPost(endpoint, payload, token);
    }

    private static String createMessageJson(Message message) {
        return String.format(
                "{\"sender\":\"%s\",\"content\":\"%s\"}",
                escapeJson(message.getSender()),
                escapeJson(message.getContent())
        );
    }

    private static String escapeJson(String value) {
        if (value == null) {
            return "";
        }

        return value
                .replace("\\", "\\\\")
                .replace("\"", "\\\"");
    }
}