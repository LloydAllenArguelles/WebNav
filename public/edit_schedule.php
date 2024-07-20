<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professor') {
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
    $availability = $_POST['availability'];

    try {
        $stmt = $pdo->prepare("UPDATE professor_availability SET availability = :availability WHERE user_id = :user_id");
        $stmt->bindParam(':availability', $availability);
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
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
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
        textarea {
            resize: vertical;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-height: 150px;
            margin-bottom: 20px;
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
</head>
<body>
    <div class="container">
        <h1>Edit Schedule</h1>
        <form method="POST" action="edit_schedule.php">
            <label for="availability">Availability:</label>
            <textarea name="availability" id="availability" required><?php echo htmlspecialchars($availability); ?></textarea>
            <div class="button-container">
                <button type="submit" class="submit-button">Save Changes</button>
                <a href="professor_availability.php" class="back-button">Back to Availability</a>
            </div>
        </form>
    </div>
</body>
</html>