<?php
require '../includes/dbh.inc.php';

if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $week = (new DateTime($date))->format('W');
    $year = (new DateTime($date))->format('Y');

    try {
        $query = "SELECT * FROM schedules WHERE week_number = :week AND year = :year";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['week' => $week, 'year' => $year]);
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($schedules);
    } catch (PDOException $e) {
        echo 'Failed to fetch schedules: ' . $e->getMessage();
    }
} else {
    echo 'Date parameter is missing.';
}
?>

