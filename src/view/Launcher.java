package view;

import storage.TokenStorage;

public class Launcher {
    public static void main(String[] args) {
        // Dev convenience: force a clean slate before Main even runs.
        // Uses the single TokenStorage mechanism (no more Preferences).
        TokenStorage.clearToken();
        System.out.println("Token forcefully wiped from the Launcher!");

        Main.main(args);
    }
}
