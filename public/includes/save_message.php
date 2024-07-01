<?php
require_once 'dbh.inc.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];

    $sql = "INSERT INTO chat_history (username, message) VALUES (:username, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'username' => $_SESSION['username'],
        'message' => $message
    ]);

    if ($stmt->rowCount() > 0) {
        http_response_code(200); 
        echo "Message saved successfully";
    } else {
        http_response_code(500); 
        echo "Failed to save message";
    }
} else {
    http_response_code(400); 
    echo "Invalid request";
}
?>

