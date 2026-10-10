<?php
declare(strict_types=1);

function h(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf_token'] ?? '', $sent)) {
        http_response_code(403);
        exit('Invalid form token. Go back and reload the page.');
    }
}

function store_student_upload(array $upload, int $studentId, PDO $pdo): void {
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return;
    }
    if (($upload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The file upload did not complete successfully.');
    }

    $maxBytes = 5 * 1024 * 1024;
    if (($upload['size'] ?? 0) > $maxBytes) {
        throw new RuntimeException('Each file must be 5 MB or smaller.');
    }

    $original = basename((string)($upload['name'] ?? 'document'));
    $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $allowed = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf',
    ];
    if (!isset($allowed[$extension])) {
        throw new RuntimeException('Only JPG, PNG and PDF files are allowed.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($upload['tmp_name']);
    if ($mime !== $allowed[$extension]) {
        throw new RuntimeException('The file extension and detected file type do not match.');
    }
    if (in_array($extension, ['jpg', 'jpeg', 'png'], true) && @getimagesize($upload['tmp_name']) === false) {
        throw new RuntimeException('The uploaded image is not a valid image.');
    }

    $directory = __DIR__ . '/uploads';
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        throw new RuntimeException('Upload directory could not be created.');
    }

    // Random names avoid overwriting files with the same original filename.
    $stored = bin2hex(random_bytes(16)) . '.' . $extension;
    $destination = $directory . '/' . $stored;
    if (!move_uploaded_file($upload['tmp_name'], $destination)) {
        throw new RuntimeException('Could not store the uploaded file.');
    }

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO student_files (student_id, original_name, stored_name, file_path, mime_type, file_size)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $studentId, $original, $stored, 'uploads/' . $stored, $mime, (int)$upload['size']
        ]);
    } catch (Throwable $e) {
        if (is_file($destination)) {
            unlink($destination);
        }
        throw $e;
    }
}

function redirect_to(string $path): never {
    header('Location: ' . $path);
    exit;
}
