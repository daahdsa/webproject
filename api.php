<?php
session_start();
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        "login" => "user" . rand(1000,9999),
        "password" => substr(md5(rand()), 0, 8),
        "data" => $data
    ];

    echo json_encode([
        "login" => $_SESSION['user']['login'],
        "password" => $_SESSION['user']['password'],
        "profile_url" => "/profile.php"
    ]);
    exit;
}

$_SESSION['user']['data'] = $data;

echo json_encode([
    "status" => "updated",
    "data" => $_SESSION['user']['data']
]);

echo json_encode([
    "status" => "все гуд",
    "data" => $data
]);