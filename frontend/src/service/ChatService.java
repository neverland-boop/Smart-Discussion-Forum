package service;

import config.ApiConfig;
import model.Message;
import storage.TokenStorage;
import utils.ApiClient;

public class ChatService {

    private ChatService() {
    }

    public static ApiClient.ApiResponse sendMessage(
            int topicId,
            Message message
    ) {
        String payload = createMessageJson(message);
        String token = TokenStorage.getToken();

        String endpoint =
                ApiConfig.BASE_URL + "/topics/" + topicId + "/posts";

        return SyncService.sendPost(
                endpoint,
                payload,
                token
        );
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
                .replace("\"", "\\\"")
                .replace("\n", "\\n")
                .replace("\r", "\\r");
    }
}