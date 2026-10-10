# Question 3 — Servlet Student Portal

## Stack
Java 17, Jakarta Servlet 6, JSP, JDBC, MySQL Connector/J, Tomcat 10.1 and Maven.

## Setup
1. Import `../database/schema.sql` into MySQL.
2. Set DB username/password in `src/main/resources/db.properties`.
3. Build: `mvn clean package`
4. Deploy `target/student-portal.war` to Tomcat 10.1.
5. Open `/student-portal/login.jsp`.
6. Demo login: `student` / `Student@123`.

## File streaming
The `FileServlet` looks up the file metadata by ID, then streams the stored file. Set environment variable `STUDENT_UPLOAD_DIR` to the absolute folder containing files created by the PHP app, for example the PHP `uploads` folder. Ensure the Tomcat process has read access to that folder.

## Note
The demo login is hard-coded for coursework and must not be used for a real portal. A production implementation should use a database-backed user table, password hashing, CSRF protection on logout/forms, HTTPS, access-control checks, and a non-public upload directory.
