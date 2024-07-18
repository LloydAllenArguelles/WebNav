<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); 
    exit();
}

require_once 'includes/dbh.inc.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - SCHEDULE</title>
    <link rel="stylesheet" href="schedule.css"> <!-- if ayaw gumana lagay muna sa public yung css idk din y ayaw gumana pag nasa assets e  -->
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
        <div class="ribbon-button-container dropdown">
            <span class="ribbon-button ribbon-trigger dropMenu">MENU</span>
            <div class="dropdown-content dropMenu">
                <a href="forum.php">FORUM</a>
                <a href="schedule.php">SCHEDULE</a>
                <a href="events.html">EVENTS</a>
                <a href="user.php">USER</a>
                <a href="settings.html">SETTINGS</a>
            </div>
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
            <a href="settings.html" class="ribbon-button">SETTINGS</a>
        </div>
    </div>

    <div class="building-schedule-container">
    <a href="professor_availability.php" class="schedule-button button">Professor Availability</a>
        <h1>Building Schedules</h1>
        <div class="building-schedule-table">
            <a href="gvschedule.php" class="schedule-button button">Gusaling Villegas</a>
            <a href="geschedule.php" class="schedule-button button">Gusaling Ejercito</a>
            <a href="gcaschedule.php" class="schedule-button button">Gusaling Aquino</a>
        </div>
        <a href="home.html" class="back-button">Back to Home</a>
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
        function navigateTo(page) {
            alert('Navigating to ' + page);
            // wala logic pa
        }
    </script>
    <script src="assets/js/buttons.js"></script>

</body>
</html>
