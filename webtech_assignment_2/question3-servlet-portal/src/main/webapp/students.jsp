<%@ page contentType="text/html; charset=UTF-8" pageEncoding="UTF-8" %>
<%@ page import="java.util.List,edu.kpriet.portal.StudentListServlet.Student" %>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Profiles | Student Portal</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="top">
  <div>
    <p class="eyebrow">STUDENT DIRECTORY</p>
    <h1>Student profiles</h1>
    <p class="top-subtitle">Student details and uploaded documents in one place.</p>
  </div>
  <form method="post" action="logout">
    <button class="secondary" type="submit">Log out</button>
  </form>
</header>

<main class="profile-list">
<%
List<Student> students = (List<Student>) request.getAttribute("students");
if (students != null && !students.isEmpty()) {
  for (Student student : students) {
    Integer profileImageId = null;
    // Select the latest uploaded image to use as the student's profile picture.
    try (java.sql.Connection connection = edu.kpriet.portal.Database.getConnection();
         java.sql.PreparedStatement imageQuery = connection.prepareStatement(
           "SELECT id FROM student_files WHERE student_id=? AND mime_type IN ('image/jpeg','image/png') ORDER BY uploaded_at DESC, id DESC LIMIT 1")) {
      imageQuery.setInt(1, student.id);
      try (java.sql.ResultSet imageResult = imageQuery.executeQuery()) {
        if (imageResult.next()) profileImageId = imageResult.getInt("id");
      }
    } catch (Exception ignored) {
      // Keep the initials placeholder visible if an image lookup fails.
    }
%>
  <article class="profile-card">
    <div class="profile-heading">
      <div class="profile-photo-wrap">
        <% if (profileImageId != null) { %>
          <img class="profile-photo" src="file?id=<%= profileImageId %>" alt="Profile picture of <%= esc(student.name) %>">
        <% } else { %>
          <div class="profile-photo profile-placeholder" aria-label="No profile picture uploaded"><%= esc(initials(student.name)) %></div>
        <% } %>
      </div>
      <div class="identity">
        <p class="student-label">STUDENT PROFILE</p>
        <h2><%= esc(student.name) %></h2>
        <p class="roll"><%= esc(student.rollNumber) %></p>
      </div>
    </div>

    <div class="details-grid">
      <div class="detail-item"><span>Email address</span><p><%= esc(student.email) %></p></div>
      <div class="detail-item"><span>Course</span><p><%= esc(student.course) %></p></div>
    </div>

    <section class="documents">
      <h3>Uploaded images and documents</h3>
      <ul class="file-list">
      <%
      try (java.sql.Connection connection = edu.kpriet.portal.Database.getConnection();
           java.sql.PreparedStatement ps = connection.prepareStatement(
             "SELECT id, original_name, mime_type FROM student_files WHERE student_id=? ORDER BY uploaded_at DESC")) {
        ps.setInt(1, student.id);
        try (java.sql.ResultSet rs = ps.executeQuery()) {
          boolean found = false;
          while (rs.next()) {
            found = true;
            String mimeType = rs.getString("mime_type");
            boolean imageFile = mimeType != null && mimeType.startsWith("image/");
      %>
        <li>
          <span class="file-icon"><%= imageFile ? "IMG" : "PDF" %></span>
          <span class="file-description">
            <a href="file?id=<%= rs.getInt("id") %>" target="_blank" rel="noopener"><%= esc(rs.getString("original_name")) %></a>
            <small><%= esc(mimeType) %></small>
          </span>
          <a class="view-file" href="file?id=<%= rs.getInt("id") %>" target="_blank" rel="noopener">View</a>
        </li>
      <%
          }
          if (!found) { %><li class="no-files">No files uploaded for this student yet.</li><% }
        }
      } catch (Exception e) { %><li class="no-files">The file list could not be loaded.</li><% }
      %>
      </ul>
    </section>
  </article>
<%
  }
} else {
%>
  <section class="empty-state">
    <div class="empty-symbol">SP</div>
    <h2>No student records yet</h2>
    <p>Add a student and upload a profile image using the PHP admin application from Experiment 2.</p>
  </section>
<%
}
%>
</main>

<%!
private String esc(String value) {
  if (value == null) return "";
  return value.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")
      .replace("\"", "&quot;").replace("'", "&#39;");
}
private String initials(String value) {
  if (value == null || value.trim().isEmpty()) return "S";
  String[] parts = value.trim().split("\\s+");
  String result = parts[0].substring(0, 1).toUpperCase();
  if (parts.length > 1) result += parts[parts.length - 1].substring(0, 1).toUpperCase();
  return result;
}
%>
</body>
</html>
