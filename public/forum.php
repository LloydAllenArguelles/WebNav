<?php
session_start();
require_once 'includes/dbh.inc.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
} else {
    $userId = $_SESSION['user_id'];
}
try {
    $stmt = $pdo->prepare("SELECT username, profile_image FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $user = NULL;
    } else if (empty($user['profile_image'])) {
        $user['profile_image'] = 'assets/front/pic.jpg';
    }
} catch (PDOException $e) {
    echo "Error fetching user details: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Forum</title>
    <link rel="stylesheet" href="assets/forum.css">
    <link rel="stylesheet" href="assets/dropdown.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
</head>
<body>
    <div class="top-ribbon">
        <div class="ribbon-button-container dropdown stay">
            <span class="ribbon-button ribbon-trigger dropView"><i class="fas fa-globe"></i> 360 VIEW</span>
            <div class="dropdown-content dropView">
                <a href="assets/tour/gv-tour.php">GV</a>
                <a href="assets/tour/gca-tour.php">GCA</a>
                <a href="assets/tour/gee-tour.php">GEE</a>
            </div>
        </div>
        <div class="ribbon-button-container stay">
            <a href="home.php" class="ribbon-button"><i class="fas fa-home"></i> HOME</a>
        </div>
        <div class="ribbon-button-container">
            <a href="forum.php" class="ribbon-button"><i class="fas fa-comments"></i> FORUM</a>
        </div>
        <div class="ribbon-button-container">
            <a href="schedule.php" class="ribbon-button"><i class="fas fa-calendar-alt"></i> SCHEDULE</a>
        </div>
        <div class="ribbon-button-container">
            <a href="events.php" class="ribbon-button"><i class="fas fa-calendar-day"></i> EVENTS</a>
        </div>
        <div class="ribbon-button-container">
            <a href="settings.php" class="ribbon-button"><i class="fas fa-cogs"></i> SETTINGS</a>
        </div>
        <div class="ribbon-button-container dropdown">
            <span class="ribbon-button ribbon-trigger dropMenu"><i class="fas fa-bars"></i> MENU</span>
            <div class="dropdown-content dropMenu">
                <a href="forum.php"><i class="fas fa-comments"></i> FORUM</a>
                <a href="schedule.php"><i class="fas fa-calendar-alt"></i> SCHEDULE</a>
                <a href="events.php"><i class="fas fa-calendar-day"></i> EVENTS</a>
                <a href="settings.php"><i class="fas fa-cogs"></i> SETTINGS</a>
            </div>
        </div>
        <div class="ribbon-button-container">
            <?php echo 
            "<a href='user.php' class='ribbon-button'><i class='fas fa-user'></i> USER: {$user['username']}</a>"
            ?>
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
        document.getElementById('message-input').addEventListener('keypress', function(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        });

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
                            document.getElementById('message-input').value = ''; // Clear the input field
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
                        chatBox.scrollTop = chatBox.scrollHeight; // Auto-scroll to the bottom
                    } else {
                        console.error('Failed to fetch chat history');
                    }
                }
            };
            xhr.send('room=' + encodeURIComponent(selectedRoom));
        }

        window.onload = fetchChatHistory; // Fetch chat history on page load for the default room
    </script>
    <script src="assets/js/buttons.js"></script>
</body>
</html>
