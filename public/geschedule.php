<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Gusaling Ejercito Estrada</title>
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

                $building_name = 'Gusaling Ejercito Estrada';

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
        session_start(); 

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        if (isset($_POST['room'])) {
            $room_id = $_POST['room'];

            $sql = "SELECT * FROM schedules WHERE room_id = :room_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':room_id' => $room_id]);
            $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h2>Gusaling Ejercito Estrada Schedule - Room {$room_id}</h2>";
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

                echo "<td>";
                if ($schedule['status'] == 'available') {
                    echo "<form method=\"POST\">";
                    echo "<input type=\"hidden\" name=\"schedule_id\" value=\"{$schedule['schedule_id']}\">";
                    echo "<button type=\"submit\" name=\"occupy_schedule\">Occupy</button>";
                    echo "</form>";
                } elseif ($schedule['status'] == 'occupied' && $schedule['user_id'] == $user_id) {
                    echo "<form method=\"POST\">";
                    echo "<input type=\"hidden\" name=\"schedule_id\" value=\"{$schedule['schedule_id']}\">";
                    echo "<button type=\"submit\" name=\"unoccupy_schedule\">Unoccupy</button>";
                    echo "</form>";
                } else {
                    echo "-";
                }
                echo "</td>";

                echo "</tr>";
            }
            echo "</tbody></table>";
        }

        if (isset($_POST['occupy_schedule'])) {
            $schedule_id = $_POST['schedule_id'];

            $sql_check_available = "SELECT * FROM schedules WHERE schedule_id = :schedule_id AND status = 'available'";
            $stmt_check_available = $pdo->prepare($sql_check_available);
            $stmt_check_available->execute([':schedule_id' => $schedule_id]);
            $schedule = $stmt_check_available->fetch(PDO::FETCH_ASSOC);

            if ($schedule) {
                $sql_update = "UPDATE schedules SET status = 'occupied', user_id = :user_id WHERE schedule_id = :schedule_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([':user_id' => $user_id, ':schedule_id' => $schedule_id]);

                if ($stmt_update->rowCount() > 0) {
                    echo "<script>alert('Schedule occupied successfully!');</script>";
                } else {
                    echo "<script>alert('Failed to occupy schedule!');</script>";
                }
            } else {
                echo "<script>alert('Schedule is already occupied or does not exist!');</script>";
            }
        }

        if (isset($_POST['unoccupy_schedule'])) {
            $schedule_id = $_POST['schedule_id'];

            $sql_check_owner = "SELECT user_id FROM schedules WHERE schedule_id = :schedule_id";
            $stmt_check_owner = $pdo->prepare($sql_check_owner);
            $stmt_check_owner->execute([':schedule_id' => $schedule_id]);
            $owner_id = $stmt_check_owner->fetchColumn();

            if ($owner_id == $user_id) {
                $sql_update = "UPDATE schedules SET status = 'available', user_id = NULL WHERE schedule_id = :schedule_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([':schedule_id' => $schedule_id]);

                if ($stmt_update->rowCount() > 0) {
                    echo "<script>alert('Schedule unoccupied successfully!');</script>";
                } else {
                    echo "<script>alert('Failed to unoccupy schedule!');</script>";
                }
            } else {
                echo "<script>alert('You do not have permission to unoccupy this schedule!');</script>";
            }
        }
        ?>
    </div>

    <!-- JavaScript functions and closing tags -->
</body>
</html>

