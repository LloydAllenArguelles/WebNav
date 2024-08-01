<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST); 


    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);

    var_dump($stmt->errorInfo()); 
    $user = $stmt->fetch();

    if ($user) {
        if ($password == $user['password_hash']) { 
            $_SESSION['user_id'] = $user['user_id']; 
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 
            header("Location: ../home.php");
            exit();
        } else {
            $error = "Invalid username or password";
            header("Location: ../index.php?error=" . urlencode($error) . "&username=" . urlencode($username));
            exit();
        }
    } else {
        $error = "Invalid username or password";
        header("Location: ../index.php?error=" . urlencode($error) . "&username=" . urlencode($username));
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
