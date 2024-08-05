<?php
session_start();
require 'includes/dbh.inc.php';
if (isset($_SESSION['user_id'])) {
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
    <title>PLM Navigation App - SETTINGS</title>
    <link rel="stylesheet" href="settings.css">
    <link rel="stylesheet" href="assets/dropdown.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        .flipbook-container {
            position: absolute;
            top: 100px; /* Adjust based on your navbar height */
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .flipbook-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        @media (max-width: 768px) {
            .flipbook-container {
                top: 60px; /* Adjust based on your mobile navbar height */
            }
        }
    </style>
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
        <div class="ribbon-button-container">
            <a href="home.php" class="ribbon-button home-button"><i class="fas fa-home"></i> HOME</a>
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

    <div class="flipbook-container">
        <iframe class="flipbook-iframe" src="https://heyzine.com/flip-book/1c5aa24dcd.html?fbclid=IwY2xjawEajmpleHRuA2FlbQIxMAABHffWq2CxfvdiTuMMvGgjg0MvOfBf23AYm8MMrl4x-OcX_L7wkYJevaCkqg_aem_D47688pSAFfY3Gvx4bB42A#page/1"></iframe>
    </div>

<!-- Chatbot Part Start -->
    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <df-messenger
      chat-icon="uploads/profile_pictures/1"
      intent="WELCOME"
      chat-title="Chatbot"
      agent-id="060d64ba-b3ff-4be9-87c6-88c97d332f18"
      language-code="en"
    ></df-messenger>
<!-- Chatbot Part End -->
    <script src="assets/js/buttons.js"></script>

</body>
</html>
