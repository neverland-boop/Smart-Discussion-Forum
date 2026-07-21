package controller;

import javafx.fxml.FXML;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import model.Message;
import service.ChatService;
import utils.ApiClient;

public class ChatController {

    @FXML
    private TextArea chatArea;

    @FXML
    private TextField messageField;

    private static final String CHAT_ENDPOINT =
            "http://localhost:8000/api/messages";

    @FXML
    private void sendMessage() {
        String content = messageField.getText();

        if (content == null || content.isBlank()) {
            return;
        }

        Message message = new Message(
                0,
                "Bidal",
                content,
                null
        );

        ApiClient.ApiResponse response =
                ChatService.sendMessage(CHAT_ENDPOINT, message);

        if (response.isSuccess()) {
            chatArea.appendText("Me: " + content + "\n");
        } else if (response.statusCode == -1) {
            chatArea.appendText("Me: " + content + " (pending)\n");
        } else {
            chatArea.appendText("Message failed to send.\n");
        }

        messageField.clear();
    }
}