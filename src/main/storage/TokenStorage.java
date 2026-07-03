package storage;

import java.io.*;

public class TokenStorage {

    // Where we will save the token
    private static final String FILE_NAME =
            System.getProperty("user.home")
       + "/.smart_forum_token";

    // Save token into the file
    public static void saveToken(String token) {

        try {
            BufferedWriter writer =
          new BufferedWriter(
         new FileWriter(FILE_NAME));

         writer.write(token);

            writer.close();

        } catch (IOException e) {
          System.out.println("Could not save token.");
        }
    }

    // Read token from the file
    public static String getToken() {

        try {
            BufferedReader reader =
                    new BufferedReader(
                            new FileReader(FILE_NAME));

            String token = reader.readLine();

            reader.close();

            return token;

        } catch (IOException e) {
            return null;
        }
    }

    // Delete the saved token
    public static void clearToken() {

        File file = new File(FILE_NAME);

        if (file.exists()) {
            file.delete();
        }
    }
}