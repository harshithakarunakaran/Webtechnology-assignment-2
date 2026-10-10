# Web Technology Assignment II — Student Management Portal

This repository contains the three parts of the U21CS501 Web Technology Assignment II.

## Project structure
- `question1-registration/` — HTML, CSS, JavaScript registration form.
- `question2-php-admin/` — PHP/MySQL CRUD and upload management.
- `question3-servlet-portal/` — Java Servlet/JSP/JDBC student profile portal.
- `database/schema.sql` — MySQL database schema.
- `screenshots/` — Add screenshots of your own running outputs here.

## Requirements
- XAMPP (Apache + MySQL/MariaDB + PHP) for Question 2.
- JDK 17 or compatible, Maven, and Apache Tomcat 10.1 for Question 3.
- Modern browser and Git.

## Question 1: Registration form
Open `question1-registration/index.html` in a browser. Client-side validation is included. This is a front-end demonstration; it does not save registrations to a server.

## Question 2: PHP and MySQL
1. Copy `question2-php-admin` into `C:\xampp\htdocs\student-management`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import `database/schema.sql`.
4. Check `question2-php-admin/config.php` and update the DB credentials if needed.
5. Open `http://localhost/student-management/index.php`.

Uploads are saved under `question2-php-admin/uploads/`. In a real deployment, keep uploads outside the public web root and serve them through authenticated download handlers.

## Question 3: Servlet portal
1. Ensure MySQL is running and import `database/schema.sql`.
2. In `question3-servlet-portal/src/main/resources/db.properties`, set database credentials.
3. Build from that directory using `mvn clean package`.
4. Deploy `target/student-portal.war` to Tomcat 10.1.
5. Open `http://localhost:8080/student-portal/login.jsp`.
6. Demo login: `student` / `Student@123` (change this before any real deployment).

The servlet uses Jakarta Servlet APIs (Tomcat 10.1). The demo login is for coursework only, not production authentication.

## Security notes
- Use prepared SQL statements and password hashing.
- The sample upload validation checks extension, MIME type, size, and generates unique server filenames. MIME checks and extension checks alone are not a complete malware defense.
- Configure the uploads directory so executable scripts cannot run from it.
- Change all demo credentials before deploying anywhere public.
- Do not commit real student data, identity documents, passwords, or private keys to GitHub.

## Submission checklist
- [ ] Run and test each question.
- [ ] Capture screenshots of your own output and put them in `screenshots/`.
- [ ] Add your name/roll number only where your instructor requires it.
- [ ] Review comments and understand the code.
- [ ] Commit and push the repository to GitHub.
