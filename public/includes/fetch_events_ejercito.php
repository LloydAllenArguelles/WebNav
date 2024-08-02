<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Use absolute path for includes
require_once __DIR__ . '/dbh.inc.php';

$today = date('Y-m-d');
$todayDayOfWeek = date('l');

$currentWeekStart = date('Y-m-d', strtotime('this week'));
$currentWeekEnd = date('Y-m-d', strtotime('this week + 6 days'));

if ($todayDayOfWeek == 'Friday') {
    $currentWeekStart = $today;
    $currentWeekEnd = date('Y-m-d', strtotime('this week + 6 days'));
}

$nextWeekStart = date('Y-m-d', strtotime('next week'));
$nextWeekEnd = date('Y-m-d', strtotime('next week + 6 days'));

$building_name = 'Gusaling Ejercito Estrada';

try {
    $sql = "SELECT e.event_name, e.time, e.day, r.room_number 
            FROM events e
            INNER JOIN rooms r ON e.room_id = r.room_id
            WHERE r.building = :building AND e.expiration_date > :today
            AND e.expiration_date BETWEEN :week_start AND :week_end
            ORDER BY e.day, e.time";

    // Fetch current week events
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':building' => $building_name,
        ':today' => $today,
        ':week_start' => $currentWeekStart,
        ':week_end' => $currentWeekEnd
    ]);
    $currentWeekEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch next week events
    $stmt->execute([
        ':building' => $building_name,
        ':today' => $today,
        ':week_start' => $nextWeekStart,
        ':week_end' => $nextWeekEnd
    ]);
    $nextWeekEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'currentWeek' => $currentWeekEvents,
        'nextWeek' => $nextWeekEvents
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>
