<?php
header('Content-Type: application/json');
require 'dbh.inc.php';

// Get the POST data
$postData = json_decode(file_get_contents('php://input'), true);
$room = $postData['room'] ?? null;
$day = $postData['day'] ?? null;

// Basic validation
if (!$room || !$day) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

try {
    // Prepare and execute the SQL query
    $sql = "SELECT * FROM schedules WHERE room_id = (
                SELECT room_id FROM rooms WHERE room_number = ?
            ) AND day_of_week = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$room, $day]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($schedules);
} catch (PDOException $e) {
    // Send a JSON error response
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}
?>