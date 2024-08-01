<?php
require_once __DIR__ . '/dbh.inc.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $currentWeek = date('W');
    $currentYear = date('Y');

    echo "Current Week: $currentWeek, Current Year: $currentYear<br>";

    // Move Old Schedules to History with user_id not NULL
    $query = "INSERT INTO schedule_history (room_id, day_of_week, start_time, end_time, status, subject, user_id, week_number, year)
              SELECT room_id, day_of_week, start_time, end_time, status, subject, user_id, week_number, year
              FROM schedules
              WHERE week_number < :week AND year <= :year AND user_id IS NOT NULL";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':week' => $currentWeek, ':year' => $currentYear]);

    echo "Schedules migrated to history.<br>";

    // Delete Old Schedules
    $query = "DELETE FROM schedules WHERE week_number < :week AND year <= :year";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':week' => $currentWeek, ':year' => $currentYear]);

    echo "Old schedules deleted.<br>";

    // Reset Current Week Schedules
    $query = "UPDATE schedules SET status = 'Available', user_id = NULL WHERE week_number = :week AND year = :year";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':week' => $currentWeek, ':year' => $currentYear]);

    echo "Current week schedules reset.<br>";

} catch (PDOException $e) {
    echo "Failed to reset schedules: " . $e->getMessage();
}
?>
