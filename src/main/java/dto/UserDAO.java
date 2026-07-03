package dao;

import database.DatabaseConnection;
import model.User;

import java.sql.*;

public class UserDAO {

    public boolean register(User user) {

        String sql =
                "INSERT INTO users(full_name,email,password) VALUES(?,?,?)";

        try (
                Connection conn =
                        DatabaseConnection.getConnection();

                PreparedStatement ps =
                        conn.prepareStatement(sql)
        ) {

            ps.setString(1,
                    user.getFullName());

            ps.setString(2,
                    user.getEmail());

            ps.setString(3,
                    user.getPassword());

            return ps.executeUpdate() > 0;

        } catch (SQLException e) {
            e.printStackTrace();
        }

        return false;
    }

    public boolean login(
            String email,
            String password
    ) {

        String sql =
                "SELECT * FROM users WHERE email=? AND password=?";

        try (
                Connection conn =
                        DatabaseConnection.getConnection();

                PreparedStatement ps =
                        conn.prepareStatement(sql)
        ) {

            ps.setString(1, email);
            ps.setString(2, password);

            ResultSet rs =
                    ps.executeQuery();

            return rs.next();

        } catch (SQLException e) {
            e.printStackTrace();
        }

        return false;
    }

    public boolean emailExists(
            String email
    ) {

        String sql =
                "SELECT * FROM users WHERE email=?";

        try (
                Connection conn =
                        DatabaseConnection.getConnection();

                PreparedStatement ps =
                        conn.prepareStatement(sql)
        ) {

            ps.setString(1, email);

            ResultSet rs =
                    ps.executeQuery();

            return rs.next();

        } catch (SQLException e) {
            e.printStackTrace();
        }

        return false;
    }
}