<?php
session_start();
require_once 'dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $room_id = $_POST['room']; 

    $sql = "INSERT INTO chat_history (room_id, user_id, username, message) VALUES (:room_id, :user_id, :username, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'room_id' => $room_id,
        'user_id' => $_SESSION['user_id'],
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
