<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

require_once 'dbh.inc.php';

$today = date('Y-m-d');
$building_name = 'Gusaling Villegas';

$selected_room_id = $_SESSION['selected_room_id'] ?? null;
$selected_date = $_GET['date'] ?? date('Y-m-d');
$selected_status = $_GET['stat'] ?? null;

if (!$selected_room_id) {
    $sql = "SELECT room_id FROM rooms WHERE building = :building LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':building' => $building_name]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    $selected_room_id = $room['room_id'] ?? null;
}

$is_professor = isset($_SESSION['role']) && $_SESSION['role'] === 'Professor';
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';

$timestamp = strtotime($selected_date);
$dayOfWeek = date('l', $timestamp);

error_log("Selected Date: " . $selected_date);
error_log("Selected Room ID: " . $selected_room_id);
error_log("Selected Status: " . $selected_status);
error_log("Day of Week: " . $dayOfWeek);

try {
    $sql = "SELECT schedules.*, users.full_name, users.profsubject
            FROM schedules 
            LEFT JOIN users ON schedules.user_id = users.user_id 
            WHERE schedules.room_id = :room_id 
            AND schedules.day_of_week = :day_of_week
            AND (schedules.expiry_date IS NULL OR schedules.expiry_date >= :selected_date)";

    $params = [
        ':room_id' => $selected_room_id,
        ':day_of_week' => $dayOfWeek,
        ':selected_date' => $selected_date
    ];

    if ($selected_status && $selected_status !== '') {
        $sql .= " AND schedules.status = :stat";
        $params[':stat'] = $selected_status;
    }

    $sql .= " ORDER BY schedules.start_time";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Number of schedules fetched: " . count($schedules));

    if (empty($schedules)) {
        echo "<p>No schedules available for this date.</p>";
    } else {
        foreach ($schedules as $schedule) {
            error_log("Schedule status: " . $schedule['status']);
            
            $subject = !empty($schedule['profsubject']) ? $schedule['profsubject'] : $schedule['subject'];

            echo "<div class='schedule-item {$schedule['status']}'>";
            echo "<div>";
            echo "<h4>" . htmlspecialchars($subject) . "</h4>";
            echo "<p>{$schedule['start_time']} - {$schedule['end_time']}</p>";
            echo "<p>Status: ";
            switch ($schedule['status']) {
                case 'Available':
                    echo "<span class=\"available\">Available</span>";
                    break;
                case 'Occupied':
                    if ($schedule['user_id'] == $_SESSION['user_id']) {
                        echo "<span class=\"occupied-own\">Approved</span>";
                    } else {
                        echo "<span class=\"occupied\">Occupied</span>";
                    }
                    break;
                case 'Pending':
                    echo "<span class=\"pending\">Pending</span>";
                    break;
                default:
                    echo "<span>" . htmlspecialchars($schedule['status']) . "</span>";
            }
            echo "</p>";
            echo "<p>Requestor: " . ($schedule['full_name'] ? htmlspecialchars($schedule['full_name']) : 'N/A') . "</p>";
            
            if (strtotime($selected_date) >= strtotime($today)) {
                echo "<form method='POST'>";
                echo "<input type='hidden' name='schedule_id' value='{$schedule['schedule_id']}'>";        
            
                if ($is_professor || $is_admin) {
                    if ($schedule['status'] == 'Available') {
                        if ($is_admin) {
                            echo "<button type='submit' name='admin_occupy_schedule'>Occupy</button>";
                        } else {
                            echo "<button type='submit' name='occupy_schedule'>Request</button>";
                        }
                    } elseif ($schedule['status'] == 'Occupied') {
                        if ($is_admin || $schedule['user_id'] == $_SESSION['user_id']) {
                            echo "<button type='submit' name='unoccupy_schedule'>Unoccupy</button>";
                        }
                    }
                    
                    if ($is_admin && $schedule['status'] == 'Pending') {
                        echo "<button type='submit' name='approve_schedule'>Approve</button>";
                        echo "<button type='submit' name='deny_schedule'>Deny</button>";
                    }
                }
                
                echo "</form>";
            } else {
                echo "<p>Actions not available for past dates</p>";
            }
            echo "</div>";
        echo "</div>";        
        }
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo "<p>An error occurred while fetching schedules. Please try again later.</p>";
}
?>
