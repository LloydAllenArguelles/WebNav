<?php
// Enable error reporting
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'dbh.inc.php';

$building_name = 'Gusaling Villegas';

$selected_room_id = null;
$selected_date = null;
$selected_status = isset($_GET['stat']) ? $_GET['stat'] : null;

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

if (isset($_GET['date'])) {
    $selectedDate = $_GET['date'];

    // Convert the date to day_of_week
    $timestamp = strtotime($selectedDate);
    $dayOfWeek = date('l', $timestamp);

    // Include the database connection file
    include 'dbh.inc.php';

    try {
        // Prepare the SQL statement to fetch schedules for the selected day of the week
        $sql = "SELECT schedules.*, users.full_name FROM schedules LEFT JOIN users ON schedules.user_id = users.user_id WHERE schedules.room_id = :room_id AND schedules.day_of_week = :day_of_week";
        $params = [
            ':room_id' => $selected_room_id,
            ':day_of_week' => $dayOfWeek
        ];

        if ($selected_status && $selected_status !== '') {
            $sql .= " AND schedules.status = :stat";
            $params[':stat'] = $selected_status;
        }

        $sql .= " ORDER BY schedules.start_time";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Fetch all schedules for the selected date
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // Debugging output
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
            echo "<p>Requestor: {$schedule['full_name']}</p>";;
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
            echo "</p>";
            echo "</div>";        
        }

        // Return the schedules as JSON
        header("Refresh: 2");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Date not specified.";
}
?>
