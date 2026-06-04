<?php

header('Content-Type: text/html; charset=UTF-8');


error_reporting(0);
ini_set('display_errors', 0);


if (
    !isset($_SERVER['PHP_AUTH_USER']) ||
    !isset($_SERVER['PHP_AUTH_PW'])
) {
    header('WWW-Authenticate: Basic realm="Admin panel"');
    header('HTTP/1.0 401 Unauthorized');
    exit();
}

$pdo = new PDO(
    "mysql:host=localhost;dbname=u82283;charset=utf8",
    "u82283",
    "7013916"
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);


$stmt = $pdo->prepare("
SELECT *
FROM admins
WHERE login = ?
");

$stmt->execute([$_SERVER['PHP_AUTH_USER']]);

$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (
    !$admin ||
    !password_verify($_SERVER['PHP_AUTH_PW'], $admin['password_hash'])
) {
    header('WWW-Authenticate: Basic realm="Admin panel"');
    header('HTTP/1.0 401 Unauthorized');
    exit();
}

$id = (int)($_GET['id'] ?? 0);

if (!filter_var($id, FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad Request']);
    exit;
}

$stmt = $pdo->prepare("
DELETE FROM application_languages
WHERE application_id = ?
");

$stmt->execute([$id]);

$stmt = $pdo->prepare("
DELETE FROM applications
WHERE id = ?
");

$stmt->execute([$id]);

header('Location: admin.php');
exit();