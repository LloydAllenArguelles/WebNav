<?php
require_once 'dbh.inc.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room'])) {
    $room_id = $_POST['room'];
    $current_user_id = $_SESSION['user_id'];

    $sql = "SELECT user_id, username, message, timestamp FROM chat_history WHERE room_id = :room_id ORDER BY timestamp ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':room_id' => $room_id]);

    if (!$stmt) {
        die('Query failed: ' . print_r($pdo->errorInfo(), true)); // Print detailed error information
    }

    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($chats as $chat) {
        $message_class = ($chat['user_id'] == $current_user_id) ? 'send' : 'receive';
        echo "<div class=\"chat-message $message_class\">{$chat['username']}: {$chat['message']} ({$chat['timestamp']})</div>";
    }
} else {
    http_response_code(400); 
    echo "Invalid request"; 
}
?>
