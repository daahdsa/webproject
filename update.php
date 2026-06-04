<?php
session_start();

error_reporting(0);
ini_set('display_errors', 0);

if (empty($_SESSION['id'])) {
    die('Нет доступа');
}

$pdo = new PDO(
    "mysql:host=localhost;dbname=u82283;charset=utf8",
    "u82283",
    "7013916"
);

$name = $_POST['full_name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$bio = $_POST['bio'];

$stmt = $pdo->prepare("
UPDATE applications
SET
full_name = ?,
phone = ?,
email = ?,
bio = ?
WHERE id = ?
");

$stmt->execute([
    $name,
    $phone,
    $email,
    $bio,
    $_SESSION['id']
]);

echo "Данные обновлены";
?>