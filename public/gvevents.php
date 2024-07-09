<?php
ob_start(); 

require_once 'includes/dbh.inc.php';

$building_name = 'Gusaling Villegas';
$sql = "SELECT * FROM schedules INNER JOIN rooms ON schedules.room_id = rooms.room_id WHERE rooms.building = :building ORDER BY schedules.start_time";
$stmt = $pdo->prepare($sql);
$stmt->execute([':building' => $building_name]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

$organized_schedules = [];

foreach ($schedules as $schedule) {
    $time_slot = "{$schedule['start_time']} - {$schedule['end_time']}";
    $day_of_week = $schedule['day_of_week'];
    $subject = $schedule['subject'];

    if (!isset($organized_schedules[$time_slot])) {
        $organized_schedules[$time_slot] = [
            'Monday' => '-',
            'Tuesday' => '-',
            'Wednesday' => '-',
            'Thursday' => '-',
            'Friday' => '-',
            'Saturday' => '-',
            'Sunday' => '-'
        ];
    }

    $organized_schedules[$time_slot][$day_of_week] = $subject;
}

ob_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Gusaling Villegas</title>
    <link rel="stylesheet" href="gvevents.css">
    <link rel="stylesheet" href="assets/chatbot.css">
    <style>
        .top-ribbon {
            background-color: #007bff;
            width: 100%;
            padding: 10px 0;
            display: flex;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .ribbon-button-container {
            margin: 0 5px;
            flex: 1 0 0; 
        }

        .ribbon-button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
            display: block;
            text-align: center;
            transition: background-color 0.3s, color 0.3s;
        }

        .ribbon-button:hover {
            background-color: #0056b3;
        }

        .building-schedule-container {
            background-color: #ffffff;
            padding: 20px 10px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px;
            width: 95%;
            overflow-x: auto;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .schedule-table th, .schedule-table td {
            border: 1px solid #007bff;
            padding: 10px;
            text-align: center;
        }

        .schedule-table th {
            background-color: #0056b3;
            color: white;
        }

        .schedule-table td {
            background-color: #e6f0ff;
        }

        .back-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 768px) {
            .ribbon-button {
                font-size: 12px;
            }

            .building-schedule-container {
                padding: 20px 5px;
            }
        }
    </style>
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

    <div class="building-schedule-container">
        <?php
        if (!empty($organized_schedules)) {
            echo "<h2>Gusaling Villegas Schedule</h2>";
            echo "<div class=\"events-table\">";
            echo "<table class=\"schedule-table\">";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Time</th>";
            echo "<th>Monday</th>";
            echo "<th>Tuesday</th>";
            echo "<th>Wednesday</th>";
            echo "<th>Thursday</th>";
            echo "<th>Friday</th>";
            echo "<th>Saturday</th>";
            echo "<th>Sunday</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            foreach ($organized_schedules as $time_slot => $schedule) {
                echo "<tr>";
                echo "<td>{$time_slot}</td>";
                echo "<td>{$schedule['Monday']}</td>";
                echo "<td>{$schedule['Tuesday']}</td>";
                echo "<td>{$schedule['Wednesday']}</td>";
                echo "<td>{$schedule['Thursday']}</td>";
                echo "<td>{$schedule['Friday']}</td>";
                echo "<td>{$schedule['Saturday']}</td>";
                echo "<td>{$schedule['Sunday']}</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p>No schedules found for Gusaling Villegas.</p>";
        }
        ?>
    </div>

    <a href="events.html" class="back-button">Back</a>

    <!-- JavaScript functions and closing tags -->

</body>
</html>
