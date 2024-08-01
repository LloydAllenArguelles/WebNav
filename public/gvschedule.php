<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/dbh.inc.php';

$building_name = 'Gusaling Villegas';

$selected_room_id = null;
$selected_date = null;
$selected_status = null;

if (isset($_POST['room'])) {
    $selected_room_id = $_POST['room'];
    $_SESSION['selected_room_id'] = $selected_room_id;
} else if (isset($_SESSION['selected_room_id'])) {
    $selected_room_id = $_SESSION['selected_room_id'];
} else {
    $sql = "SELECT room_id FROM rooms WHERE building = :building LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':building' => $building_name]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    $selected_room_id = $room['room_id'] ?? null;
}

if (isset($_POST['date'])) {
    $selected_date = $_POST['date'];
    $_SESSION['selected_date'] = $selected_date;
} else if (isset($_SESSION['selected_date'])) {
    $selected_date = $_SESSION['selected_date'];
} else {
    $selected_date = date('Y-m-d'); 
}

if (isset($_POST['stat'])) {
    $selected_status = $_POST['stat'];
    $_SESSION['selected_status'] = $selected_status;
} else if (isset($_SESSION['selected_status'])) {
    $selected_status = $_SESSION['selected_status'];
}

$is_professor = isset($_SESSION['role']) && $_SESSION['role'] === 'Professor';
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - <?php echo htmlspecialchars($building_name); ?></title>
    <link rel="stylesheet" href="assets/dropdown.css">
    <link rel="stylesheet" href="assets/schedule.css">
</head>
<body>
<header>
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
    </header>
    <div class="building-schedule-container">
        <h2><?php echo htmlspecialchars($building_name); ?> Schedule</h2>
        
        <div class="filters">
            <form method="POST" id="filterForm">
                <label for="room">Room:</label>
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

                <label for="stat">Status:</label>
                <select name="stat" id="stat" onchange="this.form.submit()">
                    <option value="" <?php echo $selected_status === null ? 'selected' : ''; ?>>Any Status</option>
                    <option value="Available" <?php echo $selected_status === 'Available' ? 'selected' : ''; ?>>Available</option>
                    <option value="Occupied" <?php echo $selected_status === 'Occupied' ? 'selected' : ''; ?>>Occupied</option>
                    <option value="Pending" <?php echo $selected_status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                </select>

                <input type="hidden" name="date" id="selected_date" value="<?php echo htmlspecialchars($selected_date); ?>">
            </form>
        </div>

        <div class="calendar">
            <div class="calendar-header">
                <button id="prev-month">&lt;</button>
                <h3 id="current-month"></h3>
                <button id="next-month">&gt;</button>
            </div>
            <div class="calendar-body">
                <div class="calendar-weekdays">
                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>
                <div class="calendar-dates" id="calendar-dates"></div>
            </div>
        </div>

        <div id="schedule-details">
            <h3>Schedule for <span id="selected-date-display"></span></h3>
            <div id="schedule-list">
                <?php
                if ($selected_room_id) {
                    $room_id = $selected_room_id;

                    // Fetch the room number
                    $sql = "SELECT room_number FROM rooms WHERE room_id = :room_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':room_id' => $room_id]);
                    $room_number = $stmt->fetchColumn();

                    // Fetch schedules with optional status filter
                    $sql = "SELECT schedules.*, users.full_name FROM schedules LEFT JOIN users ON schedules.user_id = users.user_id WHERE schedules.room_id = :room_id AND schedules.day_of_week = :day_of_week";
                    $params = [
                        ':room_id' => $room_id,
                        ':day_of_week' => date('l', strtotime($selected_date))
                    ];

                    if ($selected_status) {
                        $sql .= " AND schedules.status = :stat";
                        $params[':stat'] = $selected_status;
                    }

                    $sql .= " ORDER BY schedules.start_time";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($schedules as $schedule) {
                        echo "<div class='schedule-item {$schedule['status']}'>";
                        echo "<h4>{$schedule['subject']}</h4>";
                        echo "<p>{$schedule['start_time']} - {$schedule['end_time']}</p>";
                        echo "<p>Status: ";
                        if ($schedule['status'] == 'Available') {
                            echo "<span class=\"available\">{$schedule['status']}</span>";
                        } elseif ($schedule['status'] == 'Occupied' && $schedule['user_id'] == $_SESSION['user_id']) {
                            echo "<span class=\"occupied-own\">{$schedule['status']} (YOU)</span>";
                        } elseif ($schedule['status'] == 'Pending') {
                            echo "<span class=\"pending\">{$schedule['status']}</span>";
                        } else {
                            echo "<span class=\"occupied\">{$schedule['status']}</span>";
                        }
                        echo "</p>";

                        if ($is_professor || $is_admin) {
                            echo "<p>Requestor: {$schedule['full_name']}</p>";
                            echo "<form method='POST'>";
                            echo "<input type='hidden' name='schedule_id' value='{$schedule['schedule_id']}'>";
                            
                            if ($is_professor) {
                                if ($schedule['status'] == 'Available') {
                                    echo "<button type='submit' name='occupy_schedule'>Request</button>";
                                } elseif ($schedule['status'] == 'Occupied' && $schedule['user_id'] == $_SESSION['user_id']) {
                                    echo "<button type='submit' name='unoccupy_schedule'>Unoccupy</button>";
                                }
                            } elseif ($is_admin && $schedule['status'] == 'Pending') {
                                echo "<button type='submit' name='approve_schedule'>Approve</button>";
                                echo "<button type='submit' name='deny_schedule'>Deny</button>";
                            }
                            
                            echo "</form>";
                        }

                        echo "</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    // Handle form submissions
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

    <script> const selectedDate = "<?php echo $selected_date; ?>"; </script>
    <script src="assets/js/buttons.js"></script>
    <script src="assets/js/calendar.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var selectedDate = document.getElementById('selected_date').value;
        var selectedDateDisplay = document.getElementById('selected-date-display');

        if (selectedDate) {
            selectedDateDisplay.textContent = selectedDate;
        }
    });
    </script>


</body>
</html>