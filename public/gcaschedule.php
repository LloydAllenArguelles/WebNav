<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Gusaling Corazon Aquino</title>
    <link rel="stylesheet" href="gvschedule.css">
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

    <div class="room-dropdown">
        <form method="POST">
            <label for="room">Select Room:</label>
            <select name="room" id="room">
                <?php
                require_once 'includes/dbh.inc.php';

                $building_name = 'Gusaling Corazon Aquino';

                $sql = "SELECT room_id, room_number FROM rooms WHERE building = :building";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':building' => $building_name]);
                $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rooms as $room) {
                    echo "<option value=\"{$room['room_id']}\">{$room['room_number']}</option>";
                }
                ?>
            </select>
            <button type="submit">Show Schedule</button>
        </form>
    </div>

    <div class="building-schedule-container">
        <?php
        if (isset($_POST['room'])) {
            $room_id = $_POST['room'];

            $sql = "SELECT * FROM schedules WHERE room_id = :room_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':room_id' => $room_id]);
            $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h2>Gusaling Corazon Aquino Schedule - Room {$room_id}</h2>";
            echo "<table class=\"schedule-table\">";
            echo "<thead><tr><th>Day</th><th>Start Time</th><th>End Time</th><th>Status</th><th>Subject</th><th>Action</th></tr></thead>";
            echo "<tbody>";
            foreach ($schedules as $schedule) {
                echo "<tr>";
                echo "<td>{$schedule['day_of_week']}</td>";
                echo "<td>{$schedule['start_time']}</td>";
                echo "<td>{$schedule['end_time']}</td>";
                echo "<td>{$schedule['status']}</td>";
                echo "<td>{$schedule['subject']}</td>";
                
                if ($schedule['status'] == 'available') {
                    echo "<td>";
                    echo "<form method=\"POST\">";
                    echo "<input type=\"hidden\" name=\"schedule_id\" value=\"{$schedule['schedule_id']}\">";
                    echo "<button type=\"submit\" name=\"occupy_schedule\">Occupy</button>";
                    echo "</form>";
                    echo "</td>";
                } else {
                    echo "<td>-</td>";
                }
                
                echo "</tr>";
            }
            echo "</tbody></table>";
        }

        if (isset($_POST['occupy_schedule'])) {
            $schedule_id = $_POST['schedule_id'];
            $user_id = 1; 

            $sql_update = "UPDATE schedules SET status = 'occupied', user_id = :user_id WHERE schedule_id = :schedule_id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([':user_id' => $user_id, ':schedule_id' => $schedule_id]);

            echo "<script>alert('Schedule occupied successfully!');</script>";
        }
        ?>
    </div>

    <!-- JavaScript functions and closing tags -->
</body>
</html>
