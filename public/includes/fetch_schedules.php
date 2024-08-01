<?php
require 'includes/dbh.inc.php';
require 'includes/week_info.inc.php';

$weekInfo = getCurrentWeekInfo();
$weekNumber = $weekInfo['week'];
$year = $weekInfo['year'];

$query = "SELECT * FROM schedules WHERE week_number = :week AND year = :year";
$stmt = $pdo->prepare($query);
$stmt->execute(['week' => $weekNumber, 'year' => $year]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($schedules as $schedule) {
    echo "<div>{$schedule['room']} - {$schedule['time']} - " . ($schedule['occupied'] ? 'Occupied' : 'Available') . "</div>";
}
?>
