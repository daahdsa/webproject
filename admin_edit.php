<?php

header('Content-Type: text/html; charset=UTF-8');

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

error_reporting(0);
ini_set('display_errors', 0);

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}


if (!empty($_FILES['file'])) {

    $allowed = ['image/jpeg', 'image/png'];

    if (!in_array($_FILES['file']['type'], $allowed)) {
        exit('Invalid file type');
    }

    if ($_FILES['file']['size'] > 2 * 1024 * 1024) {
        exit('File too large');
    }

    $name = bin2hex(random_bytes(8));
    move_uploaded_file($_FILES['file']['tmp_name'], "uploads/$name");
}




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

$stmt = $pdo->prepare("
SELECT *
FROM applications
WHERE id = ?
");

$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('Пользователь не найден');
}

$languages = $pdo->query("
SELECT *
FROM programming_languages
")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
SELECT language_id
FROM application_languages
WHERE application_id = ?
");

$stmt->execute([$id]);

$userLanguages = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Редактирование</title>
</head>
<body>

<h1>Редактирование заявки</h1>

<form action="admin_update.php" method="POST">

<input type="hidden" name="id" value="<?= $user['id'] ?>">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">


<p>
ФИО<br>
<input type="text" name="full_name"
value="<?= e($user['full_name']) ?>">
</p>

<p>
Телефон<br>
<input type="text" name="phone"
value="<?= e($user['phone']) ?>">
</p>

<p>
Email<br>
<input type="email" name="email"
value="<?= e($user['email']) ?>">
</p>

<p>
Дата рождения<br>
<input type="date" name="birth_date"
value="<?= e($user['birth_date']) ?>">
</p>

<p>
Пол<br>

<label>
<input type="radio" name="gender" value="male"
<?= $user['gender']=='male'?'checked':'' ?>>
Мужской
</label>

<label>
<input type="radio" name="gender" value="female"
<?= $user['gender']=='female'?'checked':'' ?>>
Женский
</label>

<label>
<input type="radio" name="gender" value="other"
<?= $user['gender']=='other'?'checked':'' ?>>
Другое
</label>

</p>

<p>
Языки<br>

<select name="languages[]" multiple size="8">

<?php foreach ($languages as $lang): ?>

<option
value="<?= $lang['id'] ?>"
<?= in_array($lang['id'], $userLanguages) ? 'selected' : '' ?>
>
<?= htmlspecialchars($lang['name']) ?>
</option>

<?php endforeach; ?>

</select>

</p>

<p>
Биография<br>
<textarea name="bio" rows="8" cols="60"><?= htmlspecialchars($user['bio']) ?></textarea>
</p>

<p>
<label>
<input
type="checkbox"
name="contract"
value="1"
<?= $user['contract_accepted'] ? 'checked' : '' ?>
>
Контракт принят
</label>
</p>

<button type="submit">
Сохранить
</button>

</form>

</body>
</html>