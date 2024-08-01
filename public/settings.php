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
    <link rel="stylesheet" href="settings.css"> <!-- if ayaw gumana lagay muna sa public yung css idk din y ayaw gumana pag nasa assets e  -->
    <link rel="stylesheet" href="assets/dropdown.css">

</head>
<body>
    <div class="top-ribbon">
        <div class="ribbon-button-container dropdown stay">
            <span class="ribbon-button ribbon-trigger dropView">360 VIEW</span>
            <div class="dropdown-content dropView">
                <a href="assets/tour/gv-tour.php">GV</a>
                <a href="assets/tour/gca-tour.php">GCA</a>
                <a href="assets/tour/gee-tour.php">GEE</a>
            </div>
        </div>        
	<div class="ribbon-button-container">
            <a href="home.php" class="ribbon-button">HOME</a>
        </div>
        <div class="ribbon-button-container">
            <a href="forum.php" class="ribbon-button">FORUM</a>
        </div>
        <div class="ribbon-button-container">
            <a href="schedule.php" class="ribbon-button">SCHEDULE</a>
        </div>
        <div class="ribbon-button-container">
            <a href="events.php" class="ribbon-button">EVENTS</a>
        </div>
        <div class="ribbon-button-container">
            <a href="settings.php" class="ribbon-button">SETTINGS</a>
        </div>
        <div class="ribbon-button-container dropdown">
            <span class="ribbon-button ribbon-trigger dropMenu">MENU</span>
            <div class="dropdown-content dropMenu">
                <a href="forum.php">FORUM</a>
                <a href="schedule.php">SCHEDULE</a>
                <a href="events.php">EVENTS</a>
                <a href="settings.php">SETTINGS</a>
            </div>
        </div>
        <div class="ribbon-button-container">
            <?php echo 
            "<a href='user.php' class='ribbon-button'>USER: {$user['username']}</a>"
            ?>
        </div>
    </div>

    <div class="settings-container">
    <h1>Settings</h1>
        <div class="settings-table" style='flex-direction: column;'>
            <a href="privacypolicy.php" class="settings-button button">Privacy Policy</a>
            <a href="usermanual.php" class="settings-button button">How it Works?</a>
            <a href="includes/logout.php" class="settings-button button">LOG OUT</a>
        </div>
        <a href="home.php" class="back-button">Back to Home</a>
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
    <script src="assets/js/buttons.js"></script>

</body>
</html>
