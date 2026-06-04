<?php


header('Content-Type: text/html; charset=UTF-8');

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

error_reporting(0);
ini_set('display_errors', 0);




if (
    !isset($_SERVER['PHP_AUTH_USER']) ||
    !isset($_SERVER['PHP_AUTH_PW'])
) {

    header('WWW-Authenticate: Basic realm="Admin panel"');
    header('HTTP/1.0 401 Unauthorized');

    exit('Требуется авторизация');
}

$pdo = new PDO(
    "mysql:host=localhost;dbname=u82283;charset=utf8",
    "u82283",
    "7013916"
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("
SELECT *
FROM admins
WHERE login = ?
");

$stmt->execute([
    $_SERVER['PHP_AUTH_USER']
]);

$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (
    !$admin ||
    !password_verify(
        $_SERVER['PHP_AUTH_PW'],
        $admin['password_hash']
    )
) {

    header('WWW-Authenticate: Basic realm="Admin panel"');
    header('HTTP/1.0 401 Unauthorized');

    exit('Неверный логин или пароль');
}

$users = $pdo->query("
SELECT *
FROM applications
ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$stats = $pdo->query("
SELECT
    pl.name,
    COUNT(al.application_id) AS total
FROM programming_languages pl
LEFT JOIN application_languages al
    ON al.language_id = pl.id
GROUP BY pl.id, pl.name
ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админка</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            background:#f5f5f5;
            margin:20px;
        }

        table{
            border-collapse:collapse;
            width:100%;
            background:white;
        }

        th,td{
            border:1px solid #ccc;
            padding:10px;
        }

        th{
            background:#eee;
        }

        a{
            text-decoration:none;
        }

        .edit{
            color:green;
        }

        .delete{
            color:red;
        }

        .stats{
            margin-top:40px;
            background:white;
            padding:20px;
        }

    </style>
</head>

<body>

<h1>Панель администратора</h1>

<h2>Все заявки</h2>

<table>

    <tr>
        <th>ID</th>
        <th>ФИО</th>
        <th>Телефон</th>
        <th>Email</th>
        <th>Дата рождения</th>
        <th>Пол</th>
        <th>Действия</th>
    </tr>

    <?php foreach ($users as $user): ?>

        <tr>

            <td><?= $user['id'] ?></td>

            <td><?= e($user['full_name']) ?> </td>

            <td><?= e($user['phone']) ?></td>

            <td><?= e($user['email']) ?></td>

            <td><?= e($user['birth_date']) ?></td>

            <td><?= e($user['gender']) ?></td>

            <td>

                <a
                    class="edit"
                    href="admin_edit.php?id=<?= $user['id'] ?>"
                >
                    Редактировать
                </a>

                |

                <a
                    class="delete"
                    href="delete.php?id=<?= $user['id'] ?>"
                    onclick="return confirm('Удалить запись?')"
                >
                    Удалить
                </a>

            </td>

        </tr>

    <?php endforeach; ?>

</table>

<div class="stats">

    <h2>Статистика языков программирования</h2>

    <table>

        <tr>
            <th>Язык</th>
            <th>Количество пользователей</th>
        </tr>

        <?php foreach ($stats as $row): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($row['name']) ?>
                </td>

                <td>
                    <?= $row['total'] ?>
                </td>

            </tr>

        <?php endforeach; ?>

    </table>

</div>

</body>
</html>
