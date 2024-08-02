<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/dbh.inc.php';

if (!$pdo) {
    echo "Failed to connect to database.";
    exit();
}

$selectedDepartment = isset($_GET['department']) ? $_GET['department'] : '';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
}
try {
    $stmt = $pdo->prepare("SELECT DISTINCT department FROM users WHERE role = 'professor'");
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $professors = [];
    if ($selectedDepartment) {
        $stmt = $pdo->prepare("
            SELECT u.full_name, pa.availability
            FROM professor_availability pa
            JOIN users u ON pa.user_id = u.user_id
            WHERE u.role = 'professor' AND u.department = :department
        ");
        $stmt->bindParam(':department', $selectedDepartment);
        $stmt->execute();
        $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT username, profile_image FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $user = NULL;
    } else if (empty($user['profile_image'])) {
        $user['profile_image'] = 'assets/front/pic.jpg';
    }
} catch (PDOException $e) {
    echo "Error fetching user details: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Professor Availability</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 10px;
            border-radius: 8px; 
            overflow: hidden; 
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px; 
            text-align: left;
            font-size: 16px;
        }
        th {
            background-color: #007bff;
            color: #ffffff;
            text-align: center; 
            border-top-left-radius: 8px; 
            border-top-right-radius: 8px; 
        }
        tr:nth-child(even) {
            background-color: #EFF5FF;
        }
        tr:nth-child(odd) {
            background-color: #ffffff;
        }
        
        .back-button {
            display: block;
            margin-top: 20px; 
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .edit-button {
            display: block;
            margin-top: 10px; 
            text-decoration: none;
            padding: 10px 20px;
            background-color: #28a745;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .edit-button:hover {
            background-color: #218838;
        }
    </style>
    <link rel="stylesheet" href="assets/schedule.css"> 
    <link rel="stylesheet" href="assets/dropdown.css">
</head>
<body>
    <div class="top-ribbon">
        <div class="ribbon-button-container dropdown stay">
            <span class="ribbon-button ribbon-trigger dropView">360 VIEW</span>
            <div class="dropdown-content dropView">
                <a href="assets/tour/gv-tour.php">GV</a>
                <a href="assets/tour/gca-tour.php">GCA</a>
                <a href="assets/tour/gee-tour.php">GEE</a>
            </div>
        </div>        
	<div class="ribbon-button-container">
            <a href="home.php" class="ribbon-button">HOME</a>
        </div>
        <div class="ribbon-button-container">
            <a href="forum.php" class="ribbon-button">FORUM</a>
        </div>
        <div class="ribbon-button-container">
            <a href="schedule.php" class="ribbon-button">SCHEDULE</a>
        </div>
        <div class="ribbon-button-container">
            <a href="events.php" class="ribbon-button">EVENTS</a>
        </div>
        <div class="ribbon-button-container">
            <a href="settings.php" class="ribbon-button">SETTINGS</a>
        </div>
        <div class="ribbon-button-container dropdown">
            <span class="ribbon-button ribbon-trigger dropMenu">MENU</span>
            <div class="dropdown-content dropMenu">
                <a href="forum.php">FORUM</a>
                <a href="schedule.php">SCHEDULE</a>
                <a href="events.php">EVENTS</a>
                <a href="settings.php">SETTINGS</a>
            </div>
        </div>
        <div class="ribbon-button-container">
            <?php echo 
            "<a href='user.php' class='ribbon-button'>USER: {$user['username']}</a>"
            ?>
        </div>
    </div>

    <div class="building-schedule-container">
        <h1>Professor Availability</h1>
        
        <form method="GET" action="professor_availability.php">
            <label for="department">Select Department:</label>
            <select name="department" id="department" onchange="this.form.submit()">
                <option value="">Select Department</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?php echo htmlspecialchars($department['department']); ?>" <?php if ($selectedDepartment == $department['department']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($department['department']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($selectedDepartment): ?>
            <div class="professor-availability-table">
                <?php if (!empty($professors)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Professor</th>
                                <th>Availability</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($professors as $professor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($professor['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($professor['availability']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No professor availability data to display for the selected department.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <a href="schedule.php" class="back-button">Back to Schedule</a>
        <?php if ($_SESSION['role'] === 'professor'): ?>
            <a href="edit_schedule.php" class="edit-button">Edit Schedule</a>
        <?php endif; ?>
    </div>
    <script src="assets/js/buttons.js"></script>
</body>
</html>
