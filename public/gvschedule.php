<?php
session_start();
// Temporary fix: Force a user session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
    $_SESSION['role'] = 'Professor'; 
}
require_once __DIR__ . '/includes/dbh.inc.php';

$building_name = 'Gusaling Villegas';

// Use null coalescing operator for default values
$selected_room_id = $_SESSION['selected_room_id'] ?? null;
$selected_date = $_SESSION['selected_date'] ?? date('Y-m-d');
$selected_status = $_SESSION['selected_status'] ?? null;

// Handle POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['room'])) {
        $selected_room_id = $_POST['room'];
        $_SESSION['selected_room_id'] = $selected_room_id;
    }
    if (isset($_POST['date'])) {
        $selected_date = $_POST['date'];
        $_SESSION['selected_date'] = $selected_date;
    }
    if (isset($_POST['stat'])) {
        $selected_status = $_POST['stat'];
        $_SESSION['selected_status'] = $selected_status;
    }
}

// If no room is selected, get the first room for the building
if (!$selected_room_id) {
    try {
        $stmt = $pdo->prepare("SELECT room_id FROM rooms WHERE building = ? LIMIT 1");
        $stmt->execute([$building_name]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        $selected_room_id = $room['room_id'] ?? null;
    } catch (PDOException $e) {
        // Log the error and continue
        error_log("Database error: " . $e->getMessage());
    }
}

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT username, profile_image FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $user = ['username' => 'Guest', 'profile_image' => 'assets/front/pic.jpg'];
    } elseif (empty($user['profile_image'])) {
        $user['profile_image'] = 'assets/front/pic.jpg';
    }
} catch (PDOException $e) {
    // Log the error and set a default user
    error_log("Database error: " . $e->getMessage());
    $user = ['username' => 'Guest', 'profile_image' => 'assets/front/pic.jpg'];
}

// Set default roles
$is_professor = $_SESSION['role'] ?? '' === 'Professor';
$is_admin = $_SESSION['role'] ?? '' === 'Admin';

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
            <div id="schedule-container">
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
                
                    if ($selected_status && $selected_status !== '') {
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
                    echo "<script>
                            alert('Schedule request sent successfully!');
                            window.location.reload();
                          </script>";
                } else {
                    echo "<script>alert('Failed to send schedule request!');</script>";
                }
            } else {
                echo "<script>console.log('Schedule is already occupied or does not exist!');</script>";
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
        echo "Debug: Script started<br>";
session_start();
echo "Debug: Session started<br>";
require_once 'includes/dbh.inc.php';
echo "Debug: Database connection file included<br>";

if (!isset($_SESSION['user_id'])) {
    echo "Debug: User not logged in, redirecting<br>";
    header("Location: index.php");
    exit();
} else {
    echo "Debug: User logged in<br>";
    $userId = $_SESSION['user_id'];
}

    document.getElementById('show-occupied-schedules').addEventListener('click', function() {
        fetch('fetch_occupied_schedules.php')
            .then(response => response.json())
            .then(data => {
                const list = document.getElementById('occupied-schedules-list');
                list.innerHTML = ''; // Clear existing content

                if (data.length > 0) {
                    const ul = document.createElement('ul');
                    data.forEach(schedule => {
                        const li = document.createElement('li');
                        li.textContent = `Room ${schedule.room_number} - ${schedule.time_slot} - ${schedule.status}`;
                        ul.appendChild(li);
                    });
                    list.appendChild(ul);
                } else {
                    list.textContent = 'No occupied schedules found.';
                }
            })
            .catch(error => {
                console.error('Error fetching occupied schedules:', error);
            });
    });

    
    
    document.addEventListener('DOMContentLoaded', function() {
        var selectedDate = document.getElementById('selected_date').value;
        var selectedDateDisplay = document.getElementById('selected-date-display');

        if (selectedDate) {
            selectedDateDisplay.textContent = selectedDate;
        }
    });
    const schedules = JSON.parse(document.getElementById("schedules").textContent);

    // Process the schedules data (optional)

    // Optionally refresh the page after processing (adjust timeout as needed)
    setTimeout(() => {
        location.reload();
    }, 1);
    </script>
    <script>
        window.onload = function() {
            var buildingName = "<?php echo htmlspecialchars($building_name ?? ''); ?>"; // Use null coalescing operator in case $building_name is not set.
            console.log("THIS IS " + buildingName);
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/WebNav/public/includes/fetch_schedules.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log('Building name sent successfully');
                    console.log('Response:', xhr.responseText);
                } else {
                    console.log('Error sending building name');
                    console.log('Status:', xhr.status);
                    console.log('Response:', xhr.responseText);
                }
            };
            
            xhr.send('building_name=' + encodeURIComponent(buildingName));
            console.log('building_name=' + encodeURIComponent(buildingName));
        };
    </script>
    <script>
document.getElementById('stat').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});
</script>
</body>
</html>