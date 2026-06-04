<?php
session_start();

error_reporting(0);
ini_set('display_errors', 0);

$pdo = new PDO(
"mysql:host=localhost;dbname=u82283;charset=utf8",
"u82283",
"7013916"
);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("
    SELECT * FROM applications
    WHERE login = ?
    ");

    $stmt->execute([$login]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {

        $_SESSION['id'] = $user['id'];

        header('Location: edit.php');
        exit();
    }

    $error = 'Неверный логин или пароль';
}
?>

<form method="POST">

<input type="text" name="login" placeholder="Логин">

<input type="password" name="password" placeholder="Пароль">

<button>Войти</button>

</form>

<?= $error ?>