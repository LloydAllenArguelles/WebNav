<?php
header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

// Ensure the user is logged in and roles are defined
session_start();
include 'db_connection.php'; // Ensure this includes your PDO connection setup

// Check if user role is set
$is_professor = $_SESSION['role'] === 'Professor';
$is_admin = $_SESSION['role'] === 'Admin';

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
                $response['success'] = true;
                $response['message'] = 'Schedule request sent successfully!';
            } else {
                $response['message'] = 'Failed to send schedule request!';
            }
        } else {
            $response['message'] = 'Schedule is already occupied or does not exist!';
        }
    } else {
        $response['message'] = 'You do not have permission to request this schedule!';
    }
} elseif (isset($_POST['unoccupy_schedule'])) {
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
                $response['success'] = true;
                $response['message'] = 'Schedule unoccupied successfully!';
            } else {
                $response['message'] = 'Failed to unoccupy schedule!';
            }
        } else {
            $response['message'] = 'You do not have permission to unoccupy this schedule!';
        }
    } else {
        $response['message'] = 'You do not have permission to unoccupy this schedule!';
    }
} elseif (isset($_POST['approve_schedule'])) {
    if ($is_admin) {
        $schedule_id = $_POST['schedule_id'];
        $sql_update = "UPDATE schedules SET status = 'Occupied' WHERE schedule_id = :schedule_id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([':schedule_id' => $schedule_id]);

        if ($stmt_update->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Schedule approved successfully!';
        } else {
            $response['message'] = 'Failed to approve schedule!';
        }
    } else {
        $response['message'] = 'You do not have permission to approve this schedule!';
    }
} elseif (isset($_POST['deny_schedule'])) {
    if ($is_admin) {
        $schedule_id = $_POST['schedule_id'];
        $sql_update = "UPDATE schedules SET status = 'Available', user_id = NULL WHERE schedule_id = :schedule_id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([':schedule_id' => $schedule_id]);

        if ($stmt_update->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Schedule denied successfully!';
        } else {
            $response['message'] = 'Failed to deny schedule!';
        }
    } else {
        $response['message'] = 'You do not have permission to deny this schedule!';
    }
}

echo json_encode($response);
exit;
?>
