<?php
session_start();

if (empty($_SESSION['id'])) {
    die('Сначала войдите');
}


error_reporting(0);
ini_set('display_errors', 0);


$pdo = new PDO(
    "mysql:host=localhost;dbname=u82283;charset=utf8",
    "u82283",
    "7013916"
);

$stmt = $pdo->prepare("
SELECT * FROM applications
WHERE id = ?
");

$stmt->execute([$_SESSION['id']]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<form method="POST" action="update.php">

<input
type="text"
name="full_name"
value="<?= htmlspecialchars($user['full_name']) ?>"
>

<input
type="tel"
name="phone"
value="<?= htmlspecialchars($user['phone']) ?>"
>

<input
type="email"
name="email"
value="<?= htmlspecialchars($user['email']) ?>"
>

<textarea name="bio"><?= htmlspecialchars($user['bio']) ?></textarea>

<button type="submit">Сохранить</button>

</form>