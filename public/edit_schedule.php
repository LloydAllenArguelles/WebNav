<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Professor') {
    header("Location: index.php");
    exit();
}

require_once 'includes/dbh.inc.php';

if (!$pdo) {
    echo "Failed to connect to database.";
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $availabilities = $_POST['availability'];
    $formattedAvailability = implode(", ", array_filter(array_map('trim', $availabilities)));

    try {
        $stmt = $pdo->prepare("UPDATE professor_availability SET availability = :availability WHERE user_id = :user_id");
        $stmt->bindParam(':availability', $formattedAvailability);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        header("Location: professor_availability.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

try {
    $stmt = $pdo->prepare("SELECT availability FROM professor_availability WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $availability = $stmt->fetchColumn();
    $availabilityArray = explode(", ", $availability);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$times = ['7am', '8am', '9am', '10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm', '5pm'];
$timeRanges = [];
foreach ($times as $start) {
    foreach ($times as $end) {
        if ($start < $end) {
            $timeRanges[] = "$start-$end";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
            font-weight: bold;
        }
        .availability-entry {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .availability-entry select, .availability-entry button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }
        .availability-entry button {
            background-color: #dc3545;
            color: #fff;
            cursor: pointer;
        }
        .availability-entry button:hover {
            background-color: #c82333;
        }
        .add-button {
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .add-button:hover {
            background-color: #218838;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
        }
        .submit-button, .back-button {
            flex: 1;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .submit-button:hover, .back-button:hover {
            background-color: #0056b3;
        }
        .submit-button {
            margin-right: 10px;
        }
    </style>
    <script>
        function addAvailability() {
            const container = document.getElementById('availability-container');
            const entry = document.createElement('div');
            entry.className = 'availability-entry';
            entry.innerHTML = `
                <select name="availability[]">
                    <option value="">Select day and time</option>
                    <?php foreach ($days as $day): ?>
                        <?php foreach ($timeRanges as $range): ?>
                            <option value="<?php echo "$day $range"; ?>"><?php echo "$day $range"; ?></option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
                <button type="button" onclick="removeAvailability(this)">Remove</button>
            `;
            container.appendChild(entry);
        }

        function removeAvailability(button) {
            button.parentElement.remove();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Edit Schedule</h1>
        <form method="POST" action="edit_schedule.php">
            <label for="availability">Availability:</label>
            <div id="availability-container">
                <?php foreach ($availabilityArray as $slot): ?>
                    <div class="availability-entry">
                        <select name="availability[]">
                            <option value="">Select day and time</option>
                            <?php foreach ($days as $day): ?>
                                <?php foreach ($timeRanges as $range): ?>
                                    <option value="<?php echo "$day $range"; ?>" <?php echo ($slot === "$day $range") ? 'selected' : ''; ?>><?php echo "$day $range"; ?></option>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="removeAvailability(this)">Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-button" onclick="addAvailability()">Add Availability</button>
            <div class="button-container">
                <button type="submit" class="submit-button">Save Changes</button>
                <a href="Professor_availability.php" class="back-button">Back to Availability</a>
            </div>
        </form>
    </div>
</body>
</html>
