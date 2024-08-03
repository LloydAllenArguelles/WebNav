<?php
session_start();
require 'includes/dbh.inc.php';
$userLoggedIn = isset($_SESSION['user_id']);
if ($userLoggedIn) {
    $userId = $_SESSION['user_id'];
    $script = "<script>
    document.addEventListener('DOMContentLoaded', function() {
    var elements = document.querySelectorAll('.ribbon-button-container');
    elements.forEach(function(element) {
    element.classList.add('noguest');
    });
    });
    </script>";
    // Output the JavaScript
    echo $script;
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
<title>PLM Navigation App - Home</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
<link rel="stylesheet" href="assets/home.css">
<link rel="stylesheet" href="assets/dropdown.css">
<link rel="stylesheet" href="assets/hide.css">
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
    <?php if ($userLoggedIn): ?>
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
    <div class="ribbon-button-container dropdown">
        <a href='user.php' class='ribbon-button'><i class='fas fa-user'></i> USER: <?php echo htmlspecialchars($user['username']); ?></a>
    </div>
    <?php else: ?>
    <div class="ribbon-button-container guest">
        <a href="index.php" class="ribbon-button"><i class="fas fa-sign-in-alt"></i> LOGIN</a>
    </div>
    <?php endif; ?>
</div>

<div class="home-bg">
    <div class="home-container">
        <div class="text-box mission">
            <h2><i class="fas fa-bullseye"></i> Mission</h2>
            <p>Our mission is to deliver exceptional value through innovation and commitment.</p>
        </div>
        <div class="text-box vision">
            <h2><i class="fas fa-eye"></i> Vision</h2>
            <p>Our vision is to be a leader in our industry, setting the standard for quality and excellence.</p>
        </div>
        <div class="text-box values">
            <h2><i class="fas fa-handshake"></i> Values</h2>
            <p>We value integrity, teamwork, and continuous improvement in everything we do.</p>
        </div>
    </div>
</div>

<!-- Chatbot Part Start -->
<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
    chat-icon="https://assets.stickpng.com/images/580b57fbd9996e24bc43be12.png"
    intent="WELCOME"
    chat-title="Chatbot"
    agent-id="060d64ba-b3ff-4be9-87c6-88c97d332f18"
    language-code="en"
></df-messenger>
<!-- Chatbot Part End -->

<script src="assets/js/buttons.js"></script>
</body>
</html>
