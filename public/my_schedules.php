<?php
session_start();
require_once 'includes/dbh.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Professor') {
    header("Location: home.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "SELECT schedules.*, rooms.room_number, rooms.building
            FROM schedules 
            JOIN rooms ON schedules.room_id = rooms.room_id
            WHERE schedules.user_id = :user_id
            ORDER BY schedules.day_of_week, schedules.start_time";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $error_message = "An error occurred while fetching schedules. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedules</title>
    <link rel="stylesheet" href="assets/schedule.css">
</head>
<body>
    <div class="building-schedule-container">
        <h2>My Schedules</h2>
        
        <?php if (isset($error_message)): ?>
            <p><?php echo $error_message; ?></p>
        <?php else: ?>
            <?php if (empty($schedules)): ?>
                <p>You have no schedules at the moment.</p>
            <?php else: ?>
                <?php foreach ($schedules as $schedule): ?>
                    <div class="schedule-item <?php echo $schedule['status']; ?>">
                        <h4><?php echo htmlspecialchars($schedule['subject']); ?></h4>
                        <p>Day: <?php echo $schedule['day_of_week']; ?></p>
                        <p>Time: <?php echo $schedule['start_time'] . ' - ' . $schedule['end_time']; ?></p>
                        <p>Room: <?php echo htmlspecialchars($schedule['room_number']); ?></p>
                        <p>Building: <?php echo htmlspecialchars($schedule['building']); ?></p>
                        <p>Status: 
                            <span class="<?php echo strtolower($schedule['status']); ?>">
                                <?php echo $schedule['status']; ?>
                            </span>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>

        <a href="gvschedule.php" class="back-button">Back to Schedule</a>
    </div>
</body>
</html>