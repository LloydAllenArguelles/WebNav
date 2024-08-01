<?php
require 'includes/dbh.inc.php';
require 'includes/week_info.inc.php';

function getNextWeekInfo() {
    $date = new DateTime();
    $date->modify('+1 week');
    return [
        'week' => $date->format("W"),
        'year' => $date->format("Y")
    ];
}

$nextWeekInfo = getNextWeekInfo();
$nextWeek = $nextWeekInfo['week'];
$nextYear = $nextWeekInfo['year'];

$query = "INSERT INTO schedule_history (room, time, week_number, year, occupied)
          SELECT room, time, week_number, year, occupied
          FROM schedules
          WHERE week_number < :week AND year <= :year";
$stmt = $pdo->prepare($query);
$stmt->execute(['week' => $nextWeek, 'year' => $nextYear]);

$query = "DELETE FROM schedules WHERE week_number < :week AND year <= :year";
$stmt = $pdo->prepare($query);
$stmt->execute(['week' => $nextWeek, 'year' => $nextYear]);

$query = "UPDATE schedules SET occupied = 0 WHERE week_number = :week AND year = :year";
$stmt = $pdo->prepare($query);
$stmt->execute(['week' => $nextWeek, 'year' => $nextYear]);
?>
