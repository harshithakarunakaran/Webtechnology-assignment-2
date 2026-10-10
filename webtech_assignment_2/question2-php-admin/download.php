<?php
require __DIR__ . '/config.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Invalid file ID.'); }
$stmt = $pdo->prepare('SELECT original_name, stored_name, mime_type FROM student_files WHERE id=?');
$stmt->execute([$id]); $file = $stmt->fetch();
if (!$file) { http_response_code(404); exit('File not found.'); }
$path = __DIR__ . '/uploads/' . basename($file['stored_name']);
if (!is_file($path)) { http_response_code(404); exit('Stored file is missing.'); }
$allowed = ['image/jpeg', 'image/png', 'application/pdf'];
if (!in_array($file['mime_type'], $allowed, true)) { http_response_code(415); exit('Unsupported file type.'); }
header('Content-Type: ' . $file['mime_type']);
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="' . str_replace(['"', "\r", "\n"], '', basename($file['original_name'])) . '"');
readfile($path);
exit;
