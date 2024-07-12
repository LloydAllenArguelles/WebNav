<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); 
    exit();
}

require_once 'includes/dbh.inc.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Forum</title>
    <link rel="stylesheet" href="forum.css">
</head>
<body>
    <div class="top-ribbon">
        <div class="ribbon-button-container">
            <a href="assets/tour/gv-tour.html" class="ribbon-button">360 VIEW</a>
        </div>
        <div class="ribbon-button-container">
            <a href="forum.php" class="ribbon-button">FORUM</a>
        </div>
        <div class="ribbon-button-container">
            <a href="schedule.php" class="ribbon-button">SCHEDULE</a>
        </div>
        <div class="ribbon-button-container">
            <a href="events.html" class="ribbon-button">EVENTS</a>
        </div>
        <div class="ribbon-button-container">
            <a href="user.php" class="ribbon-button">USER</a>
        </div>
    </div>

    <div class="forum-container">
        <h1>Forum</h1>

        <form id="roomForm">
            <label for="room">Select Room:</label>
            <select name="room" id="room">
                <?php
                $sql = "SELECT room_id, room_number FROM rooms";
                $stmt = $pdo->query($sql);
                $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rooms as $room) {
                    echo "<option value=\"{$room['room_id']}\">Room {$room['room_number']}</option>";
                }
                ?>
            </select>
        </form>

        <div class="chat-box" id="chat-box">
        </div>

        <div class="input-container">
            <textarea id="message-input" placeholder="Type your message here..."></textarea>
            <button type="button" onclick="sendMessage()">Send</button>
        </div>
    </div>

    <!-- JavaScript Functions -->
    <script>
        document.getElementById('room').addEventListener('change', fetchChatHistory);

        function sendMessage() {
            const messageInput = document.getElementById('message-input').value.trim();
            const selectedRoom = document.getElementById('room').value;

            if (messageInput !== "") {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'includes/save_message.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            console.log('Message saved successfully');
                            fetchChatHistory(); 
                        } else {
                            console.error('Failed to save message');
                        }
                    }
                };
                xhr.send('message=' + encodeURIComponent(messageInput) + '&room=' + encodeURIComponent(selectedRoom));
            }
        }

        function fetchChatHistory() {
            const selectedRoom = document.getElementById('room').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'includes/fetch_chat_history.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const chatBox = document.getElementById('chat-box');
                        chatBox.innerHTML = xhr.responseText;
                    } else {
                        console.error('Failed to fetch chat history');
                    }
                }
            };
            xhr.send('room=' + encodeURIComponent(selectedRoom));
        }

        window.onload = fetchChatHistory; // Fetch chat history on page load for the default room
    </script>
</body>
</html>
