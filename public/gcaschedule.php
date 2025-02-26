<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/dbh.inc.php';

$building_name = 'Gusaling Corazon Aquino'; // Updated building name

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

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
    margin: 0 10px;
    display: block;
}

.ribbon-button {
    padding: 10px 10px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 20px; 
    font-size: 16px;
    transition: background-color 0.3s, color 0.3s;
}

.ribbon-button:hover {
    background-color: #0056b3;
}

.dropdown {
    position: relative;
    padding-top: 0px;
    padding-bottom: 0px;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 100px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    top:25px;
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #f1f1f1;
}

.dropdown-content.show {
    display: block;
}

.dropdown-content.dropMenu {
    display: none;
}

.ribbon-trigger {
    cursor: pointer;
    display: none;
}

.stay .ribbon-trigger {
    display: unset;
}

/* Media Query for screens less than or equal to 805px */
@media screen and (max-width: 805px) {
    .top-ribbon {
        flex-direction: row;
        align-items: flex-end;
    }

    .ribbon-button-container {
        display: none;
    }

    .ribbon-button-container.stay {
        display: unset;
    }

    .ribbon-button-container.dropdown {
        display: unset;
    }

    .dropdown-content {
        position: fixed;
        left: 0;
        right: 0;
        margin-top: 11px;
    }

    .dropdown-content.show {
        display: block;
    }

    .dropdown-content a {
        text-align: left;
    }
    
    .dropdown-content {
        border-bottom: blue 2px solid;
        border-top: blue 2px solid;
    }

    .ribbon-trigger {
        display: unset;
    }
}
</style>

<body>
<header>
<div class="top-ribbon">
        <div class="ribbon-button-container dropdown stay">
            <span class="ribbon-button ribbon-trigger dropView"><i class="fas fa-globe"></i> 360 VIEW</span>
            <div class="dropdown-content dropView">
                <a href="assets/tour/gv-tour.php">GV</a>
                <a href="assets/tour/gca-tour.php">GCA</a>
                <a href="assets/tour/gee-tour.php">GEE</a>
                <a href="assets/tour/gca-tour.php">GCA</a> <!-- Added link for new building -->
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
                    <option value="" <?php echo $selected_status === null ? 'selected' : ''; ?>>All</option>
                    <option value="available" <?php echo $selected_status === 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="booked" <?php echo $selected_status === 'booked' ? 'selected' : ''; ?>>Booked</option>
                </select>

                <label for="date">Date:</label>
                <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($selected_date); ?>" onchange="this.form.submit()">
            </form>
        </div>

        <div class="schedule">
            <?php
            $sql = "SELECT * FROM schedule WHERE room_id = :room_id AND date = :date";
            if ($selected_status !== null) {
                $sql .= " AND status = :status";
            }
            $stmt = $pdo->prepare($sql);
            $params = [
                ':room_id' => $selected_room_id,
                ':date' => $selected_date,
            ];
            if ($selected_status !== null) {
                $params[':status'] = $selected_status;
            }
            $stmt->execute($params);
            $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($schedule) {
                echo "<table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Event</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>";
                foreach ($schedule as $entry) {
                    echo "<tr>
                        <td>" . htmlspecialchars($entry['time']) . "</td>
                        <td>" . htmlspecialchars($entry['event']) . "</td>
                        <td>" . htmlspecialchars($entry['status']) . "</td>
                    </tr>";
                }
                echo "</tbody>
                </table>";
            } else {
                echo "<p>No schedule available for the selected room and date.</p>";
            }
            ?>
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
