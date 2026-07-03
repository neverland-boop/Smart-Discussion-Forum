package view;

import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.VBox;
import javafx.scene.paint.Color;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.stage.Stage;

import storage.TokenStorage;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

public class DashboardForm {

    public void start(Stage stage) {

        // ==========================
        // TITLE
        // ==========================
        Label title =
                new Label(
                        "Smart Discussion Forum"
                );

        title.setFont(
                Font.font(
                        "Arial",
                        FontWeight.BOLD,
                        32
                )
        );

        title.setTextFill(
                Color.DARKBLUE
        );

        // ==========================
        // WELCOME MESSAGE
        // ==========================
        Label welcome =
                new Label(
                        "Welcome to your Dashboard!"
                );

        welcome.setFont(
                Font.font(
                        "Arial",
                        FontWeight.BOLD,
                        24
                )
        );

        // ==========================
        // DATE AND TIME
        // ==========================
        LocalDateTime now =
                LocalDateTime.now();

        DateTimeFormatter formatter =
                DateTimeFormatter.ofPattern(
                        "dd MMMM yyyy  HH:mm:ss"
                );

        Label date =
                new Label(
                        "Logged in on: "
                                + now.format(formatter)
                );

        date.setFont(
                Font.font(16)
        );

        // ==========================
        // SAMPLE CONTENT
        // ==========================
        Label message =
                new Label(
                        "You have successfully logged in."
                );

        message.setFont(
                Font.font(18)
        );

        // ==========================
        // LOGOUT BUTTON
        // ==========================
        Button logoutButton =
                new Button("Logout");

        logoutButton.setPrefWidth(200);
        logoutButton.setPrefHeight(40);

        logoutButton.setOnAction(e -> {

            // Remove saved token
  logoutButton.setOnAction(e -> {

    TokenStorage.clearToken();

    try {
        new LoginScreen().start(stage);
    }
    catch (Exception ex) {
        ex.printStackTrace();
    }
});
        });

        

        // ==========================
        // CENTER CONTENT
        // ==========================
        VBox center =
                new VBox(25);

        center.setAlignment(
                Pos.CENTER
        );

        center.getChildren().addAll(
                title,
                welcome,
                date,
                message,
                logoutButton
        );

        // ==========================
        // ROOT
        // ==========================
        BorderPane root =
                new BorderPane();

        root.setCenter(center);
        root.setPadding(
                new Insets(30)
        );

        root.setStyle(
                "-fx-background-color: white;"
        );

        Scene scene =
                new Scene(
                        root,
                        1200,
                        700
                );

        stage.setTitle(
                "Dashboard"
        );

        stage.setScene(scene);
        stage.setMaximized(true);
        stage.show();
    }
}