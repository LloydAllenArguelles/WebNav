<?php 

$dsn = "mysql:host=localhost;dbname=kamiqduw_webnav_db";
$dbusername = "kamiqduw_admin";
$dbpassword = "nijjy0-jafSek-suchak";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>