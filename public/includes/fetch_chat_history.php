<?php
require_once 'dbh.inc.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room'])) {
    $room_id = $_POST['room'];
    $current_user_id = $_SESSION['user_id'];

    $sql = "SELECT ch.user_id, u.username, ch.message, ch.timestamp, u.role 
            FROM chat_history ch
            JOIN users u ON ch.user_id = u.user_id
            WHERE ch.room_id = :room_id 
            ORDER BY ch.timestamp ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':room_id' => $room_id]);

    $previous_user_id = null; // Variable to store previous user_id

    if ($stmt->rowCount() > 0) {
        while ($chat = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $message_class = ($chat['user_id'] == $current_user_id) ? 'send' : 'receive';

            if ($chat['role'] == 'Professor') {
                $message_class .= ' professor'; 
            }

            // Check if it's the same user as the previous message
            if ($chat['user_id'] != $previous_user_id) {
                // If not the same user, show user info
                $sanitized_username = htmlspecialchars($chat['username']);
                $sanitized_role = htmlspecialchars($chat['role']);
                $sanitized_timestamp = htmlspecialchars($chat['timestamp']);
                echo "<div class=\"chat-message chat-info $message_class\"><em>{$sanitized_username} - <strong>{$sanitized_role}</strong> ({$sanitized_timestamp})</em></div>";
            }

            // Always show the message
            $sanitized_message = htmlspecialchars($chat['message']);
            echo "<div class=\"chat-message $message_class\">{$sanitized_message}</div>";

            // Update previous_user_id
            $previous_user_id = $chat['user_id'];
        }
    } else {
        echo "No messages found for this room.";
    }
} else {
    http_response_code(400); 
    echo "Invalid request"; 
}
?>
