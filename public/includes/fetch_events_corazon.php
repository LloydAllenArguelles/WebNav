<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/dbh.inc.php';

$today = date('Y-m-d');
$todayDayOfWeek = date('l'); // Full textual representation of the current day

$currentWeekStart = date('Y-m-d', strtotime('this week'));
$currentWeekEnd = date('Y-m-d', strtotime('this week + 6 days'));

if ($todayDayOfWeek == 'Friday') {
    $currentWeekStart = $today;
    $currentWeekEnd = date('Y-m-d', strtotime('this week + 6 days'));
}

$nextWeekStart = date('Y-m-d', strtotime('next week'));
$nextWeekEnd = date('Y-m-d', strtotime('next week + 6 days'));

$building_name = 'Gusaling Corazon Aquino'; // Updated building name

try {
    // Fetch current week events
    $sql = "SELECT e.event_name, e.time, e.day, r.room_number 
            FROM events e
            INNER JOIN rooms r ON e.room_id = r.room_id
            WHERE r.building = :building AND e.expiration_date > :today
            AND e.expiration_date BETWEEN :week_start AND :week_end
            ORDER BY e.day, e.time";
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

    // Output JSON
    header('Content-Type: application/json');
    echo json_encode([
        'currentWeek' => $currentWeekEvents,
        'nextWeek' => $nextWeekEvents
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
