package edu.kpriet.portal;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.List;

@WebServlet("/students")
public class StudentListServlet extends HttpServlet {
    public static class Student {
        public int id;
        public String name, rollNumber, email, course;
        public Student(int id, String name, String rollNumber, String email, String course) {
            this.id=id; this.name=name; this.rollNumber=rollNumber; this.email=email; this.course=course;
        }
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        List<Student> students = new ArrayList<>();
        try (Connection connection = Database.getConnection();
             PreparedStatement statement = connection.prepareStatement(
                 "SELECT id, name, roll_number, email, course FROM students ORDER BY name");
             ResultSet result = statement.executeQuery()) {
            while (result.next()) {
                students.add(new Student(result.getInt("id"), result.getString("name"),
                    result.getString("roll_number"), result.getString("email"), result.getString("course")));
            }
        } catch (Exception exception) {
            throw new ServletException("Unable to load student records. Check database configuration.", exception);
        }
        request.setAttribute("students", students);
        request.getRequestDispatcher("/students.jsp").forward(request, response);
    }
}
