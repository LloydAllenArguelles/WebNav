<?php
session_start();
require_once 'includes/dbh.inc.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$selected_date = $_POST['date'] ?? date('Y-m-d');
$selected_room_id = $_POST['room'] ?? null;
$selected_status = $_POST['status'] ?? null;

$is_professor = isset($_SESSION['role']) && $_SESSION['role'] === 'Professor';
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';

if ($selected_room_id) {
    try {
        $sql = "SELECT schedules.*, users.full_name FROM schedules 
                LEFT JOIN users ON schedules.user_id = users.user_id 
                WHERE schedules.room_id = :room_id AND schedules.day_of_week = :day_of_week";
        $params = [
            ':room_id' => $selected_room_id,
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

        foreach ($schedules as &$schedule) {
            $schedule['actions'] = '';
            if ($is_professor || $is_admin) {
                $schedule['actions'] = "<form method='POST' action='handle_schedule_action.php'>";
                $schedule['actions'] .= "<input type='hidden' name='schedule_id' value='{$schedule['schedule_id']}'>";
                
                if ($is_professor) {
                    if ($schedule['status'] == 'Available') {
                        $schedule['actions'] .= "<button type='submit' name='occupy_schedule'>Request</button>";
                    } elseif ($schedule['status'] == 'Occupied' && $schedule['user_id'] == $_SESSION['user_id']) {
                        $schedule['actions'] .= "<button type='submit' name='unoccupy_schedule'>Unoccupy</button>";
                    }
                } elseif ($is_admin && $schedule['status'] == 'Pending') {
                    $schedule['actions'] .= "<button type='submit' name='approve_schedule'>Approve</button>";
                    $schedule['actions'] .= "<button type='submit' name='deny_schedule'>Deny</button>";
                }
                
                $schedule['actions'] .= "</form>";
            }
        }

        echo json_encode($schedules);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'No room selected']);
}