<?php
session_start(); 

require 'includes/dbh.inc.php'; 

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PLM Navigation App - User</title>
        <link rel="stylesheet" href="user.css">
        <link rel="stylesheet" href="assets/chatbot.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh;
                background-color: #f4f4f4;
            }

            .message-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
                max-width: 600px;
                background-color: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                text-align: center;
            }

            .no-account-message {
                font-size: 1.2em;
                color: #333;
            }

            .no-account-buttons {
                display: flex;
                gap: 20px;
                margin-top: 20px;
            }

            .button {
                padding: 10px 20px;
                background-color: #007BFF;
                color: white;
                text-decoration: none;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="message-container">
            <p class="no-account-message">User not logged in.</p>
            <div class="no-account-buttons">
                <a href="index.php" class="button">Log In</a>
                <a href="home.html" class="button">Back</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT username, email, department, student_num, program, year_level, student_status, status FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $user = [
            'username' => 'Unknown',
            'email' => 'N/A',
            'department' => 'N/A',
            'student_num' => 'N/A',
            'program' => 'N/A',
            'year_level' => 'N/A',
            'student_status' => 'N/A',
            'status' => 'N/A'
        ];
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - User</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/chatbot.css">
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
        <div class="ribbon-button-container">
            <a href="includes/logout.php" class="ribbon-button">LOGOUT</a>
        </div>
    </div>
    
    <div class="user-container">
        <h1>User Profile</h1>
        <div class="profile">
            <div class="card">
                <div>
                    <img src="assets/front/pic.jpg" id="profile-pic" alt="Profile Picture">
                    <label for="input-file">Update Image</label>
                </div>
                <input type="file" accept="image/jpeg, image/png, image/jpg" id="input-file">
                <div id="user-profile">
                    <?php
                    if (empty($user['student_num'])) {
                        echo "
                            <p id='display-name'>Name: " . htmlspecialchars($user['username']) . "</p>
                            <p id='display-email'>Email: " . htmlspecialchars($user['email']) . "</p>
                            <p id='display-department'>Department: " . htmlspecialchars($user['department']) . "</p>
                            <p id='display-status'>Status: " . htmlspecialchars($user['status']) . "</p>";
                    } else {
                        echo "
                            <p id='display-name'>Name: " . htmlspecialchars($user['username']) . "</p>
                            <p id='display-student-number'>Student Number: " . htmlspecialchars($user['student_num']) . "</p>
                            <p id='display-program'>Program: " . htmlspecialchars($user['program']) . "</p>
                            <p id='display-year-level'>Year Level: " . htmlspecialchars($user['year_level']) . "</p>
                            <p id='display-status'>Status: " . htmlspecialchars($user['student_status']) . "</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Part Start -->
    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <df-messenger
      chat-icon="https:&#x2F;&#x2F;assets.stickpng.com&#x2F;images&#x2F;580b57fbd9996e24bc43be12.png"
      intent="WELCOME"
      chat-title="Chatbot"
      agent-id="060d64ba-b3ff-4be9-87c6-88c97d332f18"
      language-code="en"
    ></df-messenger>
    <!-- Chatbot Part End -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let profilePic = document.getElementById("profile-pic");
            let inputfile = document.getElementById("input-file");

            inputfile.onchange = function(){
                profilePic.src = URL.createObjectURL(inputfile.files[0]);
            };
        });
    </script>
</body>
</html>
