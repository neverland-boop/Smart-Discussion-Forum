package view;

public class Launcher {
    public static void main(String[] args) {
        // FORCE THE WIPE HERE BEFORE MAIN EVEN RUNS
        java.util.prefs.Preferences.userNodeForPackage(Reg.class).remove("auth_token");
        System.out.println("Token forcefully wiped from the Launcher!");

        Main.main(args);
    }
}


