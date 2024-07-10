<?php

session_start();

require 'includes/dbh.inc.php'; 

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    echo "User not logged in.";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT username, student_num, program, year_level, student_status FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $user = [
            'username' => 'Unknown',
            'student_num' => 'N/A',
            'program' => 'N/A',
            'year_level' => 'N/A',
            'student_status' => 'N/A'
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
            <a href="assets/tour/tour.html" class="ribbon-button">360 VIEW</a>
        </div>
        <div class="ribbon-button-container">
            <a href="forum.html" class="ribbon-button">FORUM</a>
        </div>
        <div class="ribbon-button-container">
            <a href="schedule.html" class="ribbon-button">SCHEDULE</a>
        </div>
        <div class="ribbon-button-container">
            <a href="events.html" class="ribbon-button">EVENTS</a>
        </div>
        <div class="ribbon-button-container">
            <a href="user.php" class="ribbon-button">USER</a>
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
                    <p id="display-name">Name: <?php echo htmlspecialchars($user['username']); ?></p>
                    <p id="display-student-number">Student Number: <?php echo htmlspecialchars($user['student_num']); ?></p>
                    <p id="display-program">Program: <?php echo htmlspecialchars($user['program']); ?></p>
                    <p id="display-year-level">Year Level: <?php echo htmlspecialchars($user['year_level']); ?></p>
                    <p id="display-status">Status: <?php echo htmlspecialchars($user['student_status']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Part Start -->
    <div class="cb-button" onclick="showModal()">
        <img src="assets/img/chatbot_img.png" alt="Chatbot">
    </div>
    <div class="cb-overlay" id="cb-overlay" onclick="hideModal()"></div>
    <div class="cb-modal" id="cb-modal">
        <span class="cb-close-button" onclick="hideModal()">x</span>
        <textarea rows="10" cols="30" placeholder="Type here..."></textarea>
    </div>
    <script src="assets/chatbot.js"></script>
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
