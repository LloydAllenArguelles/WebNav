<?php
ob_start();
require_once 'includes/dbh.inc.php';

$today = date('Y-m-d');
$todayDayOfWeek = date('l'); // Full textual representation of the current day

$currentWeekStart = date('Y-m-d', strtotime('this week'));
$currentWeekEnd = date('Y-m-d', strtotime('this week + 6 days'));

if ($todayDayOfWeek == 'Friday') {
    // Show only from today to the end of the week
    $currentWeekStart = $today;
    $currentWeekEnd = date('Y-m-d', strtotime('this week + 6 days'));
}

// Adjust next week dates
$nextWeekStart = date('Y-m-d', strtotime('next week'));
$nextWeekEnd = date('Y-m-d', strtotime('next week + 6 days'));

$building_name = 'Gusaling Villegas';

// Fetch current week events
$sql = "SELECT e.event_name, e.time, e.day, r.room_number 
        FROM events e
        INNER JOIN rooms r ON e.room_id = r.room_id
        WHERE r.building = :building AND e.expiration_date > :today
        AND e.expiration_date BETWEEN :week_start AND :week_end
        ORDER BY e.day, e.time";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':building' => $building_name,
    ':today' => $today,
    ':week_start' => $currentWeekStart,
    ':week_end' => $currentWeekEnd
]);
$currentWeekEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch next week events
$stmt->execute([
    ':building' => $building_name,
    ':today' => $today,
    ':week_start' => $nextWeekStart,
    ':week_end' => $nextWeekEnd
]);
$nextWeekEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

session_start();
$user = NULL;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
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
}

ob_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Gusaling Villegas</title>
    <link rel="stylesheet" href="assets/buildingevents.css">
    <link rel="stylesheet" href="assets/dropdown.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa; 
        }

        .building-schedule-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin: 30px auto;
            width: 95%;
            max-width: 1200px;
        }

        .building-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .schedule-table th, .schedule-table td {
            border: 1px solid #e0e0e0;
            padding: 10px;
            text-align: left;
        }

        .schedule-table th {
            background-color: #f0f0f0;
        }

        .schedule-table td {
            font-size: 14px;
        }

        .no-events {
            color: #999;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }

        .back-button {
            display: block;
            width: 100px;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4285f4;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #3367d6;
        }

        /* Additional styles for table headers and rows */
        .schedule-table thead th {
            background-color: #007bff;
            color: white;
        }

        .schedule-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .schedule-table tbody tr:hover {
            background-color: #f1f1f1;
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

    <div class="building-schedule-container">
        <h2 class="building-title">Gusaling Villegas Scheduled Events</h2>

<!-- Current Week Schedule Table -->
<h3>Current Week Schedule</h3>
<table id="current-week-schedule" class="schedule-table">
    <thead>
        <tr>
            <th>Time</th>
            <th>Event Name</th>
            <th>Room</th>
        </tr>
    </thead>
    <tbody>
        <!-- Content will be dynamically inserted here -->
    </tbody>
</table>

<!-- Next Week Schedule Table -->
<h3>Next Week Schedule</h3>
<table id="next-week-schedule" class="schedule-table">
    <thead>
        <tr>
            <th>Time</th>
            <th>Event Name</th>
            <th>Room</th>
        </tr>
    </thead>
    <tbody>
        <!-- Content will be dynamically inserted here -->
    </tbody>
</table>

        <a href="events.php" class="back-button">Back</a>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    function fetchEvents() {
        const today = new Date().toISOString().split('T')[0];
        fetch(`includes/fetch_events.php?date=${today}`)
            .then(response => response.json())
            .then(data => {
                displayEvents(data.currentWeek, 'current-week-schedule');
                displayEvents(data.nextWeek, 'next-week-schedule');
            })
            .catch(error => console.error('Error fetching events:', error));
    }

    function displayEvents(events, containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container ${containerId} not found.`);
            return;
        }

        const tbody = container.querySelector('tbody');
        tbody.innerHTML = ''; // Clear previous content

        let currentDate = '';

        events.forEach(event => {
            if (event.expiration_date !== currentDate) {
                currentDate = event.expiration_date;
                const date = new Date(currentDate);
                const formattedDate = date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                tbody.innerHTML += `
                    <tr>
                        <td colspan="3"><strong>${formattedDate}</strong></td>
                    </tr>
                `;
            }
            
            tbody.innerHTML += `
                <tr>
                    <td>${event.time}</td>
                    <td>${event.event_name}</td>
                    <td>Room ${event.room_number}</td>
                </tr>
            `;
        });

        if (events.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" class="no-events">No events scheduled</td></tr>`;
        }
    }

    fetchEvents();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to toggle dropdown
    function toggleDropdown(event) {
        var dropdownContent = this.nextElementSibling;
        dropdownContent.classList.toggle('show');
        event.stopPropagation();
    }

    // Get all dropdown triggers
    var dropdownTriggers = document.querySelectorAll('.ribbon-trigger');

    // Add click event listener to each trigger
    dropdownTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', toggleDropdown);
    });

    // Close dropdowns when clicking outside
    window.addEventListener('click', function(event) {
        if (!event.target.matches('.ribbon-trigger')) {
            var dropdowns = document.querySelectorAll('.dropdown-content');
            dropdowns.forEach(function(dropdown) {
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });
});
</script>
</body>
</html>
