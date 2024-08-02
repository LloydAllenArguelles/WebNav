<?php
require_once 'dbh.inc.php';

header('Content-Type: application/json');

// Define the current date and calculate week start and end dates
$today = date('Y-m-d');
$currentWeekStart = date('Y-m-d', strtotime("{$today} -".(date('N')-1).' days'));
$currentWeekEnd = date('Y-m-d', strtotime("{$currentWeekStart} + 6 days"));

// Calculate the next week's start and end dates
$nextWeekStart = date('Y-m-d', strtotime("{$currentWeekStart} + 7 days"));
$nextWeekEnd = date('Y-m-d', strtotime("{$currentWeekEnd} + 7 days"));

// Get building name from query parameter
$building_name = isset($_GET['building']) ? $_GET['building'] : 'Gusaling Villegas';

try {
    $sql = "SELECT e.event_name, e.time, e.day, r.room_number, e.expiration_date
            FROM events e
            INNER JOIN rooms r ON e.room_id = r.room_id
            WHERE r.building = :building 
            AND e.expiration_date > :today
            AND (
                (e.expiration_date BETWEEN :current_week_start AND :current_week_end)
                OR
                (e.expiration_date BETWEEN :next_week_start AND :next_week_end)
            )
            ORDER BY e.expiration_date, e.time";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':building' => $building_name,
        ':today' => $today,
        ':current_week_start' => $currentWeekStart,
        ':current_week_end' => $currentWeekEnd,
        ':next_week_start' => $nextWeekStart,
        ':next_week_end' => $nextWeekEnd
    ]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($events);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
