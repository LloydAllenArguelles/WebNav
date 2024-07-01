<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'dbh.inc.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        if ($password == $user['password']) { 
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['username'] = $user['username'];
            header("Location: ../home.html");
            exit();
        } else {
            header("Location: ../index.html");
            exit();
        }
    } else {
        header("Location: ../index.html");
        exit();
    }
} else {
    header("Location: ../index.html");
    exit();
}