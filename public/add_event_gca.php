<?php
session_start();
require_once 'includes/dbh.inc.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: home.php");
    exit();
}

$building_name = 'Gusaling Corazon Aquino';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $event_name = filter_input(INPUT_POST, 'event_name', FILTER_SANITIZE_STRING);
    $event_name = substr($event_name, 0, 20); // Limit to 20 characters
    $room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);
    $day = date('l', strtotime($date)); // Get day of the week

    if ($event_name && $room_id && $date && $time) {
        try {
            // Check for conflicts
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
                // Insert new event
                $sql = "INSERT INTO events (event_name, room_id, day, time, expiration_date) VALUES (:event_name, :room_id, :day, :time, :expiration_date)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':event_name' => $event_name,
                    ':room_id' => $room_id,
                    ':day' => $day,
                    ':time' => $time,
                    ':expiration_date' => $date
                ]);
                $success_message = "Event added successfully!";
            }
        } catch (PDOException $e) {
            $error_message = "Error adding event: " . $e->getMessage();
        }
    } else {
        $error_message = "All fields are required.";
    }
}

// Fetch rooms for the dropdown
$sql = "SELECT room_id, room_number FROM rooms WHERE building = :building";
$stmt = $pdo->prepare($sql);
$stmt->execute([':building' => $building_name]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <option value="<?php echo $room['room_id']; ?>"><?php echo $room['room_number']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div>
                <label for="time">Time:</label>
                <input type="time" id="time" name="time" required>
            </div>
            <div>
                <input type="submit" value="Add Event">
            </div>
        </form>
        <a href="gcaevents.php">Back to GCA Events Page</a>
    </div>
</body>
</html>
