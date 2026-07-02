module com.example.login {
    requires javafx.controls;
    requires javafx.fxml;
    requires javafx.graphics;

    opens com.example.login to javafx.fxml;
    opens view to javafx.graphics, javafx.fxml;
    exports com.example.login;
}