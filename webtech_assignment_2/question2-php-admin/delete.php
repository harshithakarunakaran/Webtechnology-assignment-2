<?php
require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed.'); }
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Invalid student ID.'); }

// Remove stored files and their metadata before deleting the student record.
$stmt = $pdo->prepare('SELECT stored_name FROM student_files WHERE student_id=?');
$stmt->execute([$id]);
foreach ($stmt->fetchAll() as $file) {
    $path = __DIR__ . '/uploads/' . basename($file['stored_name']);
    if (is_file($path)) { unlink($path); }
}
$stmt = $pdo->prepare('DELETE FROM students WHERE id=?');
$stmt->execute([$id]);
$_SESSION['flash'] = $stmt->rowCount() ? 'Student deleted.' : 'Student record not found.';
redirect_to('index.php');
