<?php
session_start();
require_once 'includes/dbh.inc.php';

// Check if user is admin or professor
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Professor')) {
    header("Location: home.php");
    exit();
}

// Fetch denial reasons
$sql = "SELECT s.*, r.room_number, u1.full_name as requester, u2.full_name as denier 
        FROM schedules s
        JOIN rooms r ON s.room_id = r.room_id
        LEFT JOIN users u1 ON s.user_id = u1.user_id
        LEFT JOIN users u2 ON s.denied_by = u2.user_id
        WHERE s.reason IS NOT NULL
        ORDER BY s.schedule_id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$denials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Denial Reasons</title>
    <link rel="stylesheet" href="assets/schedule.css">
</head>
<body>
    <h1>Schedule Denial Reasons</h1>
    <table>
        <thead>
            <tr>
                <th>Room</th>
                <th>Date</th>
                <th>Time</th>
                <th>Requester</th>
                <th>Denier</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($denials as $denial): ?>
            <tr>
                <td><?php echo htmlspecialchars($denial['room_number']); ?></td>
                <td><?php echo htmlspecialchars($denial['day_of_week']); ?></td>
                <td><?php echo htmlspecialchars($denial['start_time'] . ' - ' . $denial['end_time']); ?></td>
                <td><?php echo htmlspecialchars($denial['requester']); ?></td>
                <td><?php echo htmlspecialchars($denial['denier']); ?></td>
                <td><?php echo htmlspecialchars($denial['reason']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>