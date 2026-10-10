<?php
require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';

$students = $pdo->query(
    'SELECT s.*, COUNT(f.id) AS file_count
     FROM students s LEFT JOIN student_files f ON f.student_id = s.id
     GROUP BY s.id ORDER BY s.created_at DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Management</title><link rel="stylesheet" href="style.css">
</head>
<body>
<header class="top"><div><p class="eyebrow">COLLEGE ADMIN PANEL</p><h1>Student Management</h1></div>
<a class="button" href="student-form.php">+ Add student</a></header>
<main class="wrap">
<?php if (!empty($_SESSION['flash'])): ?><p class="notice"><?= h($_SESSION['flash']); unset($_SESSION['flash']); ?></p><?php endif; ?>
<div class="table-wrap"><table>
<thead><tr><th>Name</th><th>Roll number</th><th>Email</th><th>Course</th><th>Files</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($students as $student): ?>
<tr>
<td><?= h($student['name']) ?></td><td><?= h($student['roll_number']) ?></td>
<td><?= h($student['email']) ?></td><td><?= h($student['course']) ?></td>
<td><?= (int)$student['file_count'] ?></td>
<td class="actions"><a href="view.php?id=<?= (int)$student['id'] ?>">View</a>
<a href="student-form.php?id=<?= (int)$student['id'] ?>">Edit</a>
<form method="post" action="delete.php" onsubmit="return confirm('Delete this student and their file records?');">
<input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<input type="hidden" name="id" value="<?= (int)$student['id'] ?>">
<button class="link danger" type="submit">Delete</button></form></td>
</tr>
<?php endforeach; ?>
<?php if (!$students): ?><tr><td colspan="6">No students found. Add the first record.</td></tr><?php endif; ?>
</tbody></table></div>
<p class="muted">Demo coursework project. Add authentication before using this admin panel with real student data.</p>
</main>
</body></html>
