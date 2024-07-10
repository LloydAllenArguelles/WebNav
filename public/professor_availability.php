<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

require_once 'includes/dbh.inc.php';

if (!$pdo) {
    echo "Failed to connect to database.";
    exit();
}

$selectedDepartment = isset($_GET['department']) ? $_GET['department'] : '';

try {
    $stmt = $pdo->prepare("SELECT DISTINCT department FROM users WHERE role = 'professor'");
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $professors = [];
    if ($selectedDepartment) {
        $stmt = $pdo->prepare("
            SELECT u.username, pa.availability
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Professor Availability</title>
    <link rel="stylesheet" href="schedule.css">
</head>
<body>
    <div class="top-ribbon">
        <div class="ribbon-button-container">
            <a href="assets/tour/gv-tour.html" class="ribbon-button">360 VIEW</a>
        </div>
        <div class="ribbon-button-container">
            <a href="forum.php" class="ribbon-button">FORUM</a>
        </div>
        <div class="ribbon-button-container">
            <a href="schedule.php" class="ribbon-button">SCHEDULE</a>
        </div>
        <div class="ribbon-button-container">
            <a href="events.html" class="ribbon-button">EVENTS</a>
        </div>
        <div class="ribbon-button-container">
            <a href="user.php" class="ribbon-button">USER</a>
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
                    <?php foreach ($professors as $professor): ?>
                        <div class="professor">
                            <h2><?php echo htmlspecialchars($professor['username']); ?></h2>
                            <p>Available: <?php echo htmlspecialchars($professor['availability']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No professor availability data to display.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <a href="schedule.php" class="back-button">Back to Schedule</a>
    </div>
</body>
</html>
