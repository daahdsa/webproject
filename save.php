<?php
header('Content-Type: text/html; charset=UTF-8');


error_reporting(0);
ini_set('display_errors', 0);

function setValue($name, $value) {
    setcookie($name . '_value', $value);
}

function setError($name) {
    setcookie($name . '_error', '1');
}

$pdo = new PDO(
    "mysql:host=localhost;dbname=u82283;charset=utf8",
    "u82283",
    "7013916"
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$birth = $_POST['birth_date'] ?? '';
$gender = $_POST['gender'] ?? '';
$bio = trim($_POST['bio'] ?? '');
$langs = $_POST['languages'] ?? [];
$contract = isset($_POST['contract']);

$hasErrors = false;

setValue('full_name', $name);
setValue('phone', $phone);
setValue('email', $email);
setValue('birth_date', $birth);
setValue('gender', $gender);
setValue('bio', $bio);
setValue('languages', json_encode($langs));
setValue('contract', $contract);

if (!preg_match("/^[\p{L}\s]{2,150}$/u", $name)) {
    setError('full_name');
    $hasErrors = true;
}

if (!preg_match("/^[0-9+\-\s]{7,30}$/", $phone)) {
    setError('phone');
    $hasErrors = true;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setError('email');
    $hasErrors = true;
}

if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $birth)) {
    setError('birth_date');
    $hasErrors = true;
}

if (!in_array($gender, ['male','female','other'])) {
    setError('gender');
    $hasErrors = true;
}

if (!preg_match("/^[\p{L}\p{N}\s.,!?()\-]{10,2000}$/u", $bio)) {
    setError('bio');
    $hasErrors = true;
}

if (empty($langs)) {
    setcookie('languages_error', '1');
    $hasErrors = true;
}

if (!$contract) {
    setcookie('contract_error', '1');
    $hasErrors = true;
}

if ($hasErrors) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT id FROM programming_languages");
$valid = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($langs as $l) {
    if (!in_array($l, $valid)) {
        setcookie('languages_error', '1');
        header("Location: index.php");
        exit();
    }
}

$login = 'user' . rand(1000,9999);

$password = bin2hex(random_bytes(4));

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
INSERT INTO applications
(full_name, phone, email, birth_date, gender, bio,
contract_accepted, login, password_hash)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([$name, $phone, $email, $birth, $gender, $bio, 1, $login, $passwordHash]);

$id = $pdo->lastInsertId();

$stmt = $pdo->prepare("
INSERT INTO application_languages (application_id, language_id)
VALUES (?, ?)
");

echo "
<h2>Данные сохранены</h2>

<p>Ваш логин: <b>$login</b></p>
<p>Ваш пароль: <b>$password</b></p>
";
exit();

foreach ($langs as $l) {
    $stmt->execute([$id, $l]);
}

setcookie('save', '1', 1799913600);

