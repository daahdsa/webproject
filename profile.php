<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo "No user";
    exit;
}

echo "<pre>";
print_r($_SESSION['user']);