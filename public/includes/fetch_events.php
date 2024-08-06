<?php
session_start();
require_once 'dbh.inc.php';
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set default values and handle session variables
$today = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$building_name = isset($_GET['building']) ? $_GET['building'] : 'Gusaling Villegas';

if (!isset($_SESSION['selected_building'])) {
    $_SESSION['selected_building'] = $building_name;
}

$currentWeekStart = date('Y-m-d', strtotime('monday this week', strtotime($today)));
$currentWeekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($today)));
$nextWeekStart = date('Y-m-d', strtotime('monday next week', strtotime($today)));
$nextWeekEnd = date('Y-m-d', strtotime('sunday next week', strtotime($today)));

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

    // Log the number of events fetched
    error_log("Number of events fetched: " . count($events));

    $currentWeekEvents = array_filter($events, function($event) use ($currentWeekStart, $currentWeekEnd) {
        return $event['expiration_date'] >= $currentWeekStart && $event['expiration_date'] <= $currentWeekEnd;
    });

    $nextWeekEvents = array_filter($events, function($event) use ($nextWeekStart, $nextWeekEnd) {
        return $event['expiration_date'] >= $nextWeekStart && $event['expiration_date'] <= $nextWeekEnd;
    });

    foreach ($events as &$event) {
        $event['formatted_date'] = date('F j, Y', strtotime($event['expiration_date']));
    }

    $response = [
        'currentWeek' => array_values($currentWeekEvents),
        'nextWeek' => array_values($nextWeekEvents)
    ];

    // Log the response
    error_log("Response: " . json_encode($response));

    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred while fetching events. Please try again later.']);
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    echo json_encode(['error' => 'An unexpected error occurred. Please try again later.']);
}
?>