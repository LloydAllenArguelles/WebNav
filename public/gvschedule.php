<?php
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/dbh.inc.php';

// Fetch building name dynamically based on the page
$building_name = 'Gusaling Villegas'; // Updated building name

// Default selected room and day
$selected_room_id = null;
$selected_day = null;
$selected_status = null;

// Update selected room and day based on form submission
if (isset($_POST['room'])) {
    $selected_room_id = $_POST['room'];
    $_SESSION['selected_room_id'] = $selected_room_id;
} else {
    // Auto-select first room if none is selected
    if (empty($selected_room_id)) {
        $sql = "SELECT room_id FROM rooms WHERE building = :building LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':building' => $building_name]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        $selected_room_id = $room['room_id'] ?? null;
    }
}

if (isset($_POST['day'])) {
    $selected_day = $_POST['day'];
    $_SESSION['selected_day'] = $selected_day;
} else {
    $selected_day = null; // Reset day filter
}

if (isset($_POST['stat'])) {
    $selected_status = $_POST['stat'];
    $_SESSION['selected_status'] = $selected_status;
} else {
    $selected_status = null; // Reset status filter
}

// Determine if the current user is a professor
$is_professor = isset($_SESSION['role']) && $_SESSION['role'] === 'Professor';
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Gusaling Villegas</title>
    <link rel="stylesheet" href="assets/schedule.css">
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
        <div class="ribbon-button-container stay">
            <a href="home.php" class="ribbon-button">HOME</a>
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
    </div>

    <div class="filters">
        <div class="room-dropdown">
            <form method="POST" id="filterForm">
                <label for="room">Select Room:</label>
                <select name="room" id="room" onchange="this.form.submit()">
                    <?php
                    $sql = "SELECT room_id, room_number FROM rooms WHERE building = :building";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':building' => $building_name]);
                    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($rooms as $room) {
                        $selected = $selected_room_id == $room['room_id'] ? 'selected' : '';
                        echo "<option value=\"{$room['room_id']}\" $selected>{$room['room_number']}</option>";
                    }
                    ?>
                </select>

                <label for="day">Select Day:</label>
                <select name="day" id="day" onchange="this.form.submit()">
                    <option value="" <?php echo $selected_day === null ? 'selected' : ''; ?>>All Days</option>
                    <option value="Monday" <?php echo $selected_day === 'Monday' ? 'selected' : ''; ?>>Monday</option>
                    <option value="Tuesday" <?php echo $selected_day === 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
                    <option value="Wednesday" <?php echo $selected_day === 'Wednesday' ? 'selected' : ''; ?>>Wednesday</option>
                    <option value="Thursday" <?php echo $selected_day === 'Thursday' ? 'selected' : ''; ?>>Thursday</option>
                    <option value="Friday" <?php echo $selected_day === 'Friday' ? 'selected' : ''; ?>>Friday</option>
                </select>
                
                <label for="stat">Select Status:</label>
                <select name="stat" id="stat" onchange="this.form.submit()">
                    <option value="" <?php echo $selected_status === null ? 'selected' : ''; ?>>Any Status</option>
                    <option value="Available" <?php echo $selected_status === 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="Occupied" <?php echo $selected_status === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                    <option value="Pending" <?php echo $selected_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                </select>
            </form>
        </div>
    </div>

    <div class="building-schedule-container">
        <?php
        if ($selected_room_id) {
            $room_id = $selected_room_id;

            // Fetch the room number
            $sql = "SELECT room_number FROM rooms WHERE room_id = :room_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':room_id' => $room_id]);
            $room_number = $stmt->fetchColumn();

            echo "<h2>{$building_name} Schedule - Room {$room_number}</h2>";

            // Fetch schedules with optional day and status filters
            $sql = "SELECT schedules.*, users.full_name FROM schedules LEFT JOIN users ON schedules.user_id = users.user_id WHERE schedules.room_id = :room_id";
            $params = [':room_id' => $room_id];

            if ($selected_day) {
                $sql .= " AND day_of_week = :day";
                $params[':day'] = $selected_day;
            }

            if ($selected_status) {
                $sql .= " AND schedules.status = :stat";
                $params[':stat'] = $selected_status;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<table class=\"schedule-table\">";
            echo "<thead><tr><th>Day</th><th>Start Time</th><th>End Time</th><th>Status</th><th>Subject</th>";
            if ($is_professor|| $is_admin) {
                echo "<th>Requestor</th><th>Action</th>";
            }
            echo "</tr></thead>";
            echo "<tbody>";
            foreach ($schedules as $schedule) {
                echo "<tr>";
                echo "<td>{$schedule['day_of_week']}</td>";
                echo "<td>{$schedule['start_time']}</td>";
                echo "<td>{$schedule['end_time']}</td>";
                if ($schedule['status'] == 'Available') 
                    {echo "<td class=\"available\">{$schedule['status']}</td>";} 
                elseif ($schedule['status'] == 'Occupied' && $schedule['user_id'] == $_SESSION['user_id']) 
                    {echo "<td class=\"occupied-own\">{$schedule['status']} (YOU)</td>";} 
                elseif ($schedule['status'] == 'Pending') 
                    {echo "<td class=\"pending\">{$schedule['status']}</td>";} 
                else 
                    {echo "<td class=\"occupied\">{$schedule['status']}</td>";}
                echo "<td>{$schedule['subject']}</td>";

                if ($is_professor) {
                    echo "<td>{$schedule['full_name']}</td>";
                    echo "<td>";
                    if ($schedule['status'] == 'Available') {
                        echo "<form method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"schedule_id\" value=\"{$schedule['schedule_id']}\">";
                        echo "<button type=\"submit\" name=\"occupy_schedule\">Request</button>";
                        echo "</form>";
                    } elseif ($schedule['status'] == 'Occupied' && $schedule['user_id'] == $_SESSION['user_id']) {
                        echo "<form method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"schedule_id\" value=\"{$schedule['schedule_id']}\">";
                        echo "<button type=\"submit\" name=\"unoccupy_schedule\">Unoccupy</button>";
                        echo "</form>";
                    } else {
                        echo "-";
                    }
                    echo "</td>";
                }
                elseif ($is_admin) {
                    echo "<td>{$schedule['full_name']}</td>";
                    echo "<td>";
                    if ($schedule['status'] == 'Pending' && $is_admin) {
                        echo "<form method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"schedule_id\" value=\"{$schedule['schedule_id']}\">";
                        echo "<button type=\"submit\" name=\"approve_schedule\">Approve</button>";
                        echo "<button type=\"submit\" name=\"deny_schedule\">Deny</button>";
                        echo "</form>";
                    } else {
                        echo "-";
                    }
                    echo "</td>";
                }

                echo "</tr>";
            }
            echo "</tbody></table>";
        }

        if (isset($_POST['occupy_schedule'])) {
            if ($is_professor) {
                $schedule_id = $_POST['schedule_id'];

                $sql_check_available = "SELECT * FROM schedules WHERE schedule_id = :schedule_id AND status = 'Available'";
                $stmt_check_available = $pdo->prepare($sql_check_available);
                $stmt_check_available->execute([':schedule_id' => $schedule_id]);
                $schedule = $stmt_check_available->fetch(PDO::FETCH_ASSOC);

                if ($schedule) {
                    $sql_update = "UPDATE schedules SET status = 'Pending', user_id = :user_id WHERE schedule_id = :schedule_id";
                    $stmt_update = $pdo->prepare($sql_update);
                    $stmt_update->execute([':user_id' => $_SESSION['user_id'], ':schedule_id' => $schedule_id]);

                    if ($stmt_update->rowCount() > 0) {
                        echo "<script>alert('Schedule request sent successfully!');</script>";
                    } else {
                        echo "<script>alert('Failed to send schedule request!');</script>";
                    }
                } else {
                    echo "<script>alert('Schedule is already occupied or does not exist!');</script>";
                }
            } else {
                echo "<script>alert('You do not have permission to request this schedule!');</script>";
            }
        }

        if (isset($_POST['unoccupy_schedule'])) {
            if ($is_professor) {
                $schedule_id = $_POST['schedule_id'];

                $sql_check_owner = "SELECT user_id FROM schedules WHERE schedule_id = :schedule_id";
                $stmt_check_owner = $pdo->prepare($sql_check_owner);
                $stmt_check_owner->execute([':schedule_id' => $schedule_id]);
                $owner_id = $stmt_check_owner->fetchColumn();

                if ($owner_id == $_SESSION['user_id']) {
                    $sql_update = "UPDATE schedules SET status = 'Available', user_id = NULL WHERE schedule_id = :schedule_id";
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
            } else {
                echo "<script>alert('You do not have permission to unoccupy this schedule!');</script>";
            }
        }

        if (isset($_POST['approve_schedule'])) {
            if ($is_admin) {
                $schedule_id = $_POST['schedule_id'];

                $sql_update = "UPDATE schedules SET status = 'Occupied' WHERE schedule_id = :schedule_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([':schedule_id' => $schedule_id]);

                if ($stmt_update->rowCount() > 0) {
                    echo "<script>alert('Schedule approved successfully!');</script>";
                } else {
                    echo "<script>alert('Failed to approve schedule!');</script>";
                }
            } else {
                echo "<script>alert('You do not have permission to approve this schedule!');</script>";
            }
        }

        if (isset($_POST['deny_schedule'])) {
            if ($is_admin) {
                $schedule_id = $_POST['schedule_id'];

                $sql_update = "UPDATE schedules SET status = 'Available', user_id = NULL WHERE schedule_id = :schedule_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([':schedule_id' => $schedule_id]);

                if ($stmt_update->rowCount() > 0) {
                    echo "<script>alert('Schedule denied successfully!');</script>";
                } else {
                    echo "<script>alert('Failed to deny schedule!');</script>";
                }
            } else {
                echo "<script>alert('You do not have permission to deny this schedule!');</script>";
            }
        }
        ?>
    </div>

    <script src="assets/js/buttons.js"></script>
</body>
</html>