<?php
ob_start(); 
require_once 'includes/dbh.inc.php';

$building_name = 'Gusaling Corazon Aquino';
$sql = "SELECT e.event_name, e.time, e.day, r.room_number 
        FROM events e
        INNER JOIN rooms r ON e.room_id = r.room_id
        WHERE r.building = :building
        ORDER BY e.day, e.time";
$stmt = $pdo->prepare($sql);
$stmt->execute([':building' => $building_name]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

session_start();
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

ob_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Gusaling Corazon Aquino</title>
    <link rel="stylesheet" href="assets/buildingevents.css">
    <link rel="stylesheet" href="assets/dropdown.css">
    <style>
        body {
            background-color: #f8f9fa; 
        }

        .building-schedule-container {
            background-color: #ffffff; 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: center;
        }

        .building-title {
            margin-top: 0;
            color: #000000; 
        }

        .schedule-card {
            border: 1px solid #007bff;
            border-radius: 8px;
            padding: 10px; 
            width: 250px; 
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; 
            display: inline-block; 
            margin-right: 15px; 
            vertical-align: top; 
        }

        .event-name {
            background-color: #007bff; 
            color: #ffffff; 
            padding: 8px;
            border-radius: 8px 8px 0 0;
            margin-bottom: 10px; 
        }

        .event-details {
            background-color: #e6f0ff;
            padding: 10px;
            border-radius: 0 0 8px 8px;
        }

        .back-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
            display: inline-block; 
            margin-top: 20px; 
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 768px) {
            .schedule-card {
                width: 100%;
            }
        }
    </style>
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
        </div>        <div class="ribbon-button-container stay">
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

    <div class="building-schedule-container">
        <h2 class="building-title">Gusaling Corazon Aquino Scheduled Events</h2>
        <?php
        if (!empty($events)) {
            foreach ($events as $event) {
                echo "<div class=\"schedule-card\">";
                echo "<div class=\"event-name\">{$event['event_name']}</div>";
                echo "<div class=\"event-details\">";
                echo "<p><strong>Time:</strong> {$event['time']}</p>";
                echo "<p><strong>Day:</strong> {$event['day']}</p>";
                echo "<p><strong>Room:</strong> {$event['room_number']}</p>";
                echo "</div>"; 
                echo "</div>";
            }
        } else {
            echo "<p>No events found for Gusaling Corazon Aquino.</p>";
        }
        ?>
    </div>

    <!-- Back button to navigate back to events page -->
    <a href="events.html" class="back-button">Back</a>

    <!-- JavaScript functions and closing tags -->
    <script src="assets/js/buttons.js"></script>

</body>
</html>
