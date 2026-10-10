package edu.kpriet.portal;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

@WebServlet("/file")
public class FileServlet extends HttpServlet {
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        int fileId;
        try { fileId = Integer.parseInt(request.getParameter("id")); }
        catch (Exception e) { response.sendError(400, "Invalid file ID"); return; }

        // The file path is retrieved from metadata, but only its stored basename is used.
        try (Connection connection = Database.getConnection();
             PreparedStatement statement = connection.prepareStatement(
                 "SELECT original_name, stored_name, mime_type FROM student_files WHERE id=?")) {
            statement.setInt(1, fileId);
            try (ResultSet result = statement.executeQuery()) {
                if (!result.next()) { response.sendError(404, "File not found"); return; }
                String original = result.getString("original_name").replaceAll("[\\r\\n\"]", "");
                String stored = Paths.get(result.getString("stored_name")).getFileName().toString();
                String mime = result.getString("mime_type");
                if (!mime.equals("image/jpeg") && !mime.equals("image/png") && !mime.equals("application/pdf")) {
                    response.sendError(415, "Unsupported file type"); return;
                }

                // Set STUDENT_UPLOAD_DIR to the absolute directory used by the PHP upload app.
                String configured = System.getenv("STUDENT_UPLOAD_DIR");
                Path directory = configured == null || configured.isBlank()
                    ? Paths.get(System.getProperty("user.home"), "student-portal-uploads")
                    : Paths.get(configured);
                Path file = directory.resolve(stored).normalize();
                if (!file.getParent().equals(directory.toAbsolutePath().normalize()) && !file.getParent().equals(directory.normalize())) {
                    response.sendError(400, "Invalid file path"); return;
                }
                if (!Files.isRegularFile(file)) { response.sendError(404, "Stored file is missing"); return; }

                response.setContentType(mime);
                response.setHeader("X-Content-Type-Options", "nosniff");
                response.setHeader("Content-Disposition", "inline; filename=\"" + original + "\"");
                try (InputStream input = Files.newInputStream(file);
                     OutputStream output = response.getOutputStream()) {
                    input.transferTo(output);
                }
            }
        } catch (Exception exception) {
            throw new ServletException("Unable to retrieve file.", exception);
        }
    }
}
