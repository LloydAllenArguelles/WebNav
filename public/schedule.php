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
    <title>PLM Navigation App - SCHEDULE</title>
    <link rel="stylesheet" href="schedule.css"> <!-- if ayaw gumana lagay muna sa public yung css idk din y ayaw gumana pag nasa assets e  -->
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
            <a href="events.html" class="ribbon-button">EVENTS</a>        </div>
        <div class = "ribbon-button-container">
            <a href="user.php" class="ribbon-button">USER</a>
        </div>
    </div>

    <div class="building-schedule-container">
    <a href="professor_availability.php" class="schedule-button">Professor Availability</a>
        <h1>Building Schedules</h1>
        <div class="building-schedule-table">
            <a href="gvschedule.php" class="schedule-button">Gusaling Villegas</a>
            <a href="geschedule.php" class="schedule-button">Gusaling Ejercito</a>
            <a href="gcaschedule.php" class="schedule-button">Gusaling Aquino</a>
        </div>
        <a href="home.html" class="back-button">Back to Home</a>
    </div>

<!-- Chatbot Part Start -->
    <!-- Button -->
    <div class="cb-button" onclick="showModal()">
        <img src="assets/img/chatbot_img.png" alt="Chatbot">
    </div>
    <!-- Overlay -->
    <div class="cb-overlay" id="cb-overlay" onclick="hideModal()"></div>
    <!-- Modal -->
    <div class="cb-modal" id="cb-modal">
        <span class="cb-close-button" onclick="hideModal()">x</span>
        <textarea rows="10" cols="30" placeholder="Type here..."></textarea>
    </div>
    <script src="assets/chatbot.js"></script>
    <!-- Chatbot Part End -->

    <script>
        function navigateTo(page) {
            alert('Navigating to ' + page);
            // wala logic pa
        }
    </script>

</body>
</html>
