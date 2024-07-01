<?php 

$dsn = "mysql:host=localhost;dbname=webnav_db";
$dbusername = "root";
$dbpassword = "";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Test query to fetch data
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    var_dump($users); // Output fetched data


} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
