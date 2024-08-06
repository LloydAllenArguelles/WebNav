<?php
session_start();
require_once 'includes/dbh.inc.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: home.php");
    exit();
}

$building_name = 'Gusaling Corazon Aquino';

// Handle event removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_event'])) {
    $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
    
    if ($event_id) {
        try {
            $sql = "DELETE FROM events WHERE event_id = :event_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':event_id' => $event_id]);
            $success_message = "Event removed successfully!";
        } catch (PDOException $e) {
            $error_message = "Error removing event: " . $e->getMessage();
        }
    }
}

// Fetch all events
$sql = "SELECT e.event_id, e.event_name, r.room_number, e.day, e.time, e.expiration_date 
        FROM events e
        INNER JOIN rooms r ON e.room_id = r.room_id
        WHERE r.building = :building
        ORDER BY e.expiration_date, e.time";
$stmt = $pdo->prepare($sql);
$stmt->execute([':building' => $building_name]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Events - PLM Navigation App</title>
    <link rel="stylesheet" href="assets/removeevent.css">
</head>
<body>
    <div class="container">
        <h2>Remove Events</h2>
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Room</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($event['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($event['day']); ?></td>
                        <td><?php echo htmlspecialchars($event['time']); ?></td>
                        <td><?php echo htmlspecialchars($event['expiration_date']); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to remove this event?');">
                                <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                                <button type="submit" name="remove_event">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="gcaevents.php">Back to GV Events Page</a>
    </div>
</body>
</html>