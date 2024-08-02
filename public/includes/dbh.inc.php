<?php
$dsn = "mysql:host=localhost;dbname=kamiqduw_webnav_db";
$dbusername = "kamiqduw_admin";
$dbpassword = "nijjy0-jafSek-suchak";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Send a JSON error response
    header('Content-Type: application/json');
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}
?>