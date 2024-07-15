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

    if ($stmt->rowCount() > 0) {
        while ($chat = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $message_class = ($chat['user_id'] == $current_user_id) ? 'send' : 'receive';

            if ($chat['role'] == 'professor') {
                $message_class .= ' professor'; 
            }

            echo "<div class=\"chat-message $message_class\">{$chat['username']}: {$chat['message']} ({$chat['timestamp']})</div>";
        }
    } else {
        echo "No messages found for this room.";
    }
} else {
    http_response_code(400); 
    echo "Invalid request"; 
}
?>
