<?php
session_start();
require_once 'includes/dbh.inc.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Professor')) {
    header("Location: home.php");
    exit();
}

$building_name = 'Gusaling Villegas';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = filter_input(INPUT_POST, 'event_name', FILTER_SANITIZE_STRING);
    $event_name = substr($event_name, 0, 20); // Limit to 20 characters
    $room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);
    $day = date('l', strtotime($date)); // Get day of the week

    if ($event_name && $room_id && $date && $time) {
        try {
            $sql = "SELECT room_number COLLATE utf8mb4_unicode_ci AS room_number FROM rooms WHERE room_id = :room_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':room_id' => $room_id]);
            $room_number = $stmt->fetchColumn();

            if (!$room_number) {
                throw new Exception("Room not found for the given room_id");
            }

            $sql = "SELECT COUNT(*) FROM events WHERE room_id = :room_id AND expiration_date = :expiration_date AND time = :time";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':room_id' => $room_id,
                ':expiration_date' => $date,
                ':time' => $time
            ]);
            $conflict_count = $stmt->fetchColumn();

            if ($conflict_count > 0) {
                $error_message = "There is already an event scheduled at the same time in this room.";
            } else {
                $sql = "INSERT INTO events (event_name, room_id, room_number, day, time, expiration_date) 
                        VALUES (:event_name, :room_id, :room_number COLLATE utf8mb4_unicode_ci, :day, :time, :expiration_date)";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    ':event_name' => $event_name,
                    ':room_id' => $room_id,
                    ':room_number' => $room_number,
                    ':day' => $day,
                    ':time' => $time,
                    ':expiration_date' => $date
                ]);

                if ($result) {
                    $success_message = "Event added successfully!";
                } else {
                    throw new Exception("Failed to insert event into database");
                }
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "All fields are required and must be valid.";
    }
}

try {
    $sql = "SELECT room_id, room_number COLLATE utf8mb4_unicode_ci AS room_number 
            FROM rooms 
            WHERE building COLLATE utf8mb4_unicode_ci = :building 
            ORDER BY room_number COLLATE utf8mb4_unicode_ci";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':building' => $building_name]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching rooms: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - PLM Navigation App</title>
    <link rel="stylesheet" href="assets/addevent.css">
</head>
<body>
    <div class="container">
        <h2>Add New Event</h2>
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
    <div>
        <label for="event_name">Event Name (max 20 characters):</label>
        <input type="text" id="event_name" name="event_name" maxlength="20" required>
    </div>
    <div>
        <label for="room_id">Room:</label>
        <select id="room_id" name="room_id" required>
            <?php foreach ($rooms as $room): ?>
                <option value="<?php echo $room['room_id']; ?>"><?php echo htmlspecialchars($room['room_number']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
    </div>
    <div>
    <label for="time">Time (24-hour format, HH:MM):</label>
    <input type="text" id="time" name="time" required pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" placeholder="HH:MM">
    </div>
    <div>
        <input type="submit" value="Add Event">
    </div>
</form>
        <a href="gvevents.php">Back to GV Events Page</a>
    </div>
    <script>
document.getElementById('time').addEventListener('input', function (e) {
    var time = e.target.value;
    var timePattern = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
    
    if (!timePattern.test(time)) {
        e.target.setCustomValidity('Please enter a valid time in 24-hour format (HH:MM)');
    } else {
        e.target.setCustomValidity('');
    }
});
</script>
</body>
</html>