package edu.kpriet.portal;

import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.util.Properties;

public final class Database {
    private static final Properties PROPERTIES = new Properties();

    static {
        try (InputStream input = Database.class.getClassLoader().getResourceAsStream("db.properties")) {
            if (input == null) throw new IllegalStateException("db.properties was not found");
            PROPERTIES.load(input);
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (Exception exception) {
            throw new ExceptionInInitializerError(exception);
        }
    }

    private Database() {}

    public static Connection getConnection() throws Exception {
        return DriverManager.getConnection(
            PROPERTIES.getProperty("db.url"),
            PROPERTIES.getProperty("db.username"),
            PROPERTIES.getProperty("db.password", "")
        );
    }
}
