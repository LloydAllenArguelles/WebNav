<?php
include 'includes/dbh.inc.php';
include 'includes/week_info.inc.php';

if ($_POST['action'] == 'occupy') {
    $scheduleId = $_POST['id'];
    $weekNumber = $_POST['week'];
    $year = $_POST['year'];

    $query = "UPDATE schedules SET occupied = 1 WHERE id = :id AND week_number = :week AND year = :year";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $scheduleId, 'week' => $weekNumber, 'year' => $year]);
}

$currentWeekInfo = getCurrentWeekInfo();
if ($weekNumber < $currentWeekInfo['week'] && $year <= $currentWeekInfo['year']) {
    echo "Cannot occupy past schedules.";
    exit;
}


?>
