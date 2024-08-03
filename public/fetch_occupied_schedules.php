<?php
session_start();
require_once 'includes/dbh.inc.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $sql = "SELECT r.room_number, s.time_slot, s.status FROM schedules s 
            JOIN rooms r ON s.room_id = r.room_id 
            WHERE s.status = 'Occupied'";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $occupied_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($occupied_schedules);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
