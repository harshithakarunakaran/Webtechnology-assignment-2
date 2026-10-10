package edu.kpriet.portal;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;
import org.mindrot.jbcrypt.BCrypt;
import java.io.IOException;

// Coursework demo login. Replace with a database-backed user table for real deployments.
@WebServlet("/login")
public class LoginServlet extends HttpServlet {
    private static final String DEMO_USER = "student";
    // BCrypt hash for the demo password Student@123.
    private static final String DEMO_HASH = "$2a$10$Q9y5nYw3bY8s3fKX6K8W2e6s4l4mQWqvX0Qn2lP8YwQ7w9Z8q9e1a";

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        String username = request.getParameter("username");
        String password = request.getParameter("password");

        // To keep the sample self-contained, compare the demo password directly.
        // For a production system, store BCrypt hashes in a users table and use BCrypt.checkpw().
        if (DEMO_USER.equals(username) && "Student@123".equals(password)) {
            HttpSession old = request.getSession(false);
            if (old != null) old.invalidate();
            HttpSession session = request.getSession(true);
            session.setAttribute("loggedIn", true);
            session.setAttribute("username", DEMO_USER);
            session.setMaxInactiveInterval(15 * 60);
            response.sendRedirect(request.getContextPath() + "/students");
        } else {
            request.setAttribute("error", "Invalid username or password.");
            request.getRequestDispatcher("/login.jsp").forward(request, response);
        }
    }
}
