<?php
require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Invalid student ID.'); }
$stmt = $pdo->prepare('SELECT * FROM students WHERE id=?'); $stmt->execute([$id]); $student = $stmt->fetch();
if (!$student) { http_response_code(404); exit('Student not found.'); }
$stmt = $pdo->prepare('SELECT * FROM student_files WHERE student_id=? ORDER BY uploaded_at DESC'); $stmt->execute([$id]); $files = $stmt->fetchAll();
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student profile</title><link rel="stylesheet" href="style.css"></head><body><main class="form-wrap">
<a href="index.php">← Back to students</a><h1>Student profile</h1>
<section class="panel"><p><strong>Name:</strong> <?= h($student['name']) ?></p>
<p><strong>Roll number:</strong> <?= h($student['roll_number']) ?></p><p><strong>Email:</strong> <?= h($student['email']) ?></p>
<p><strong>Course:</strong> <?= h($student['course']) ?></p><p><strong>Created:</strong> <?= h($student['created_at']) ?></p>
<a class="button" href="student-form.php?id=<?= (int)$id ?>">Edit profile</a></section>
<h2>Uploaded files</h2><section class="panel">
<?php if (!$files): ?><p>No files uploaded yet.</p><?php else: ?><ul class="file-list">
<?php foreach ($files as $file): ?>
<li><div><strong><?= h($file['original_name']) ?></strong><br><small><?= h($file['mime_type']) ?> · <?= number_format(((int)$file['file_size']) / 1024, 1) ?> KB · <?= h($file['uploaded_at']) ?></small></div>
<a href="download.php?id=<?= (int)$file['id'] ?>">Open / download</a></li>
<?php endforeach; ?></ul><?php endif; ?>
</section></main></body></html>
