<?php
require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$student = ['name'=>'', 'roll_number'=>'', 'email'=>'', 'course'=>''];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    if (!$student) { http_response_code(404); exit('Student not found.'); }
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $roll = trim((string)($_POST['roll_number'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $course = trim((string)($_POST['course'] ?? ''));
    $student = compact('name') + ['roll_number'=>$roll, 'email'=>$email, 'course'=>$course];

    if ($name === '' || $roll === '' || $course === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a name, roll number, valid email and course.';
    } else {
        try {
            if ($id) {
                $stmt = $pdo->prepare('UPDATE students SET name=?, roll_number=?, email=?, course=? WHERE id=?');
                $stmt->execute([$name, $roll, $email, $course, $id]);
                $studentId = $id;
            } else {
                $stmt = $pdo->prepare('INSERT INTO students (name, roll_number, email, course) VALUES (?, ?, ?, ?)');
                $stmt->execute([$name, $roll, $email, $course]);
                $studentId = (int)$pdo->lastInsertId();
                $id = $studentId;
            }

            foreach (['profile_image', 'document'] as $field) {
                if (isset($_FILES[$field])) store_student_upload($_FILES[$field], $studentId, $pdo);
            }
            $_SESSION['flash'] = 'Student details saved successfully.';
            redirect_to('view.php?id=' . $studentId);
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000'
                ? 'That roll number is already in use.' : 'Could not save the record. Check your database setup.';
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $id ? 'Edit' : 'Add' ?> Student</title><link rel="stylesheet" href="style.css"></head><body>
<main class="form-wrap"><a href="index.php">← Back to students</a><h1><?= $id ? 'Edit student' : 'Add student' ?></h1>
<?php if ($error): ?><p class="error-box"><?= h($error) ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="panel">
<input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<label>Full name<input name="name" required maxlength="120" value="<?= h($student['name'] ?? '') ?>"></label>
<label>Roll number<input name="roll_number" required maxlength="40" value="<?= h($student['roll_number'] ?? '') ?>"></label>
<label>Email<input type="email" name="email" required maxlength="190" value="<?= h($student['email'] ?? '') ?>"></label>
<label>Course<input name="course" required maxlength="100" value="<?= h($student['course'] ?? '') ?>"></label>
<label>Profile image (JPG/PNG, max 5 MB)<input type="file" name="profile_image" accept=".jpg,.jpeg,.png"></label>
<label>ID proof / certificate (PDF/JPG/PNG, max 5 MB)<input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"></label>
<p class="muted">Files selected here are added to the student's file list. Existing files are not overwritten.</p>
<button type="submit"><?= $id ? 'Save changes' : 'Create student' ?></button></form></main></body></html>
