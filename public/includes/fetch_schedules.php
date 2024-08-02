<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['date'])) {
    $selectedDate = $_GET['date'];

    // Convert the date to day_of_week
    $timestamp = strtotime($selectedDate);
    $dayOfWeek = date('l', $timestamp);

    // Debugging output
    echo "Selected Date: $selectedDate<br>";
    echo "Day of Week: $dayOfWeek<br>";

    // Include the database connection file
    include 'dbh.inc.php';

    try {
        // Prepare the SQL statement to fetch schedules for the selected day of the week
        $sql = "SELECT * FROM schedules WHERE day_of_week = :day_of_week";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':day_of_week', $dayOfWeek, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch all schedules for the selected date
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debugging output
        echo "<pre>";
        print_r($schedules);
        echo "</pre>";

        // Return the schedules as JSON
        echo json_encode($schedules);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Date not specified.";
}
?>

