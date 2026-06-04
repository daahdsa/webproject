<?php
header('Content-Type: text/html; charset=UTF-8');

error_reporting(0);
ini_set('display_errors', 0);

$errors = [];
$values = [];

$fields = [
    'full_name',
    'phone',
    'email',
    'birth_date',
    'gender',
    'bio'
];

foreach ($fields as $field) {

    $errors[$field] = !empty($_COOKIE[$field . '_error']);

    if ($errors[$field]) {
        setcookie($field . '_error', '', time() - 3600);
    }

    $values[$field] = $_COOKIE[$field . '_value'] ?? '';

    if (isset($_COOKIE[$field . '_value'])) {
        setcookie($field . '_value', '', time() - 3600);
    }
}

$languages = [];

if (!empty($_COOKIE['languages_value'])) {
    $languages = json_decode($_COOKIE['languages_value'], true);

    setcookie('languages_value', '', time() - 3600);
}

$contract = !empty($_COOKIE['contract_value']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Форма заявки</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f3f5f7;
}

.container {
    width: 600px;
    margin: 30px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

h1 {
    text-align: center;
}

label {
    display: block;
    margin-top: 12px;
    font-weight: bold;
}

input,
textarea,
select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

textarea {
    min-height: 120px;
}

.error {
    border: 2px solid red;
    background: #ffe6e6;
}

.error-message {
    color: red;
    font-size: 14px;
}

.row {
    display: flex;
    gap: 20px;
    margin-top: 5px;
}

button {
    margin-top: 20px;
    width: 100%;
    padding: 10px;
    background: #2d6cdf;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

button:hover {
    background: #1e4fa8;
}

.success {
    color: green;
    text-align: center;
    font-weight: bold;
}

</style>
</head>
<body>

<div class="container">

<h1>Форма заявки</h1>

<?php
if (!empty($_COOKIE['save'])) {
    echo '<p class="success">Данные успешно сохранены</p>';
    setcookie('save', '', time() - 3600);
}
?>

<form action="save.php" method="POST">

<label>ФИО</label>

<?php
if ($errors['full_name']) {
    echo '<div class="error-message">
    Допустимы только буквы и пробелы, максимум 150 символов
    </div>';
}
?>

<input
type="text"
name="full_name"
value="<?= htmlspecialchars($values['full_name']) ?>"
class="<?= $errors['full_name'] ? 'error' : '' ?>"
>

<label>Телефон</label>

<?php
if ($errors['phone']) {
    echo '<div class="error-message">
    Допустимы цифры, пробелы, + и -
    </div>';
}
?>

<input
type="tel"
name="phone"
value="<?= htmlspecialchars($values['phone']) ?>"
class="<?= $errors['phone'] ? 'error' : '' ?>"
>

<label>Email</label>

<?php
if ($errors['email']) {
    echo '<div class="error-message">
    Введите корректный email
    </div>';
}
?>

<input
type="email"
name="email"
value="<?= htmlspecialchars($values['email']) ?>"
class="<?= $errors['email'] ? 'error' : '' ?>"
>

<label>Дата рождения</label>

<?php
if ($errors['birth_date']) {
    echo '<div class="error-message">
    Укажите корректную дату
    </div>';
}
?>

<input
type="date"
name="birth_date"
value="<?= htmlspecialchars($values['birth_date']) ?>"
class="<?= $errors['birth_date'] ? 'error' : '' ?>"
>

<label>Пол</label>

<?php
if ($errors['gender']) {
    echo '<div class="error-message">
    Выберите допустимый пол
    </div>';
}
?>

<div class="row">

<label>
<input type="radio" name="gender" value="male"
<?= $values['gender'] == 'male' ? 'checked' : '' ?>>
Мужской
</label>

<label>
<input type="radio" name="gender" value="female"
<?= $values['gender'] == 'female' ? 'checked' : '' ?>>
Женский
</label>

<label>
<input type="radio" name="gender" value="other"
<?= $values['gender'] == 'other' ? 'checked' : '' ?>>
Другое
</label>

</div>

<label>Любимые языки программирования</label>

<?php
if (!empty($_COOKIE['languages_error'])) {
    echo '<div class="error-message">
    Выберите минимум один язык
    </div>';

    setcookie('languages_error', '', time() - 3600);
}
?>

<select name="languages[]" multiple size="6">

<?php
$pdo = new PDO("mysql:host=localhost;dbname=u82283;charset=utf8","u82283","7013916");

$stmt = $pdo->query("SELECT * FROM programming_languages");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $selected = in_array($row['id'], $languages) ? 'selected' : '';

    echo "<option value='{$row['id']}' $selected>
    {$row['name']}
    </option>";
}
?>

</select>

<label>Биография</label>

<?php
if ($errors['bio']) {
    echo '<div class="error-message">
    Допустимы буквы, цифры, знаки препинания
    </div>';
}
?>

<textarea
name="bio"
class="<?= $errors['bio'] ? 'error' : '' ?>"
><?= htmlspecialchars($values['bio']) ?></textarea>

<br><br>

<label>
<input type="checkbox" name="contract" value="1"
<?= $contract ? 'checked' : '' ?>>
С контрактом ознакомлен
</label>

<?php
if (!empty($_COOKIE['contract_error'])) {

    echo '<div class="error-message">
    Необходимо принять контракт
    </div>';

    setcookie('contract_error', '', time() - 3600);
}
?>

<button type="submit">Сохранить</button>

</form>
</div>

</body>
</html>