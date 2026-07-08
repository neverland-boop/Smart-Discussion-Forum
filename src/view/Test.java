package view;

public class Test {
    public static void main(String[] args) {
        try {
            Class.forName("org.sqlite.JDBC");
            System.out.println("✅ SUCCESS: SQLite Driver is working perfectly!");
        } catch (ClassNotFoundException e) {
            System.out.println("❌ FAILED: The driver library is still missing from this path.");
        }
    }
}