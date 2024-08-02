<?php
require_once 'includes/dbh.inc.php';

// Get the current date
$today = date('Y-m-d');

// Update the expiration dates
$sql = "UPDATE events 
        SET expiration_date = CASE
            WHEN day = 'Monday' THEN DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), INTERVAL 1 DAY)
            WHEN day = 'Tuesday' THEN DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), INTERVAL 2 DAY)
            WHEN day = 'Wednesday' THEN DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), INTERVAL 3 DAY)
            WHEN day = 'Thursday' THEN DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), INTERVAL 4 DAY)
            WHEN day = 'Friday' THEN DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), INTERVAL 5 DAY)
            WHEN day = 'Saturday' THEN DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), INTERVAL 6 DAY)
            WHEN day = 'Sunday' THEN DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), INTERVAL 7 DAY)
        END
        WHERE expiration_date IS NULL";

$stmt = $pdo->prepare($sql);
$stmt->execute();

echo "Expiration dates updated.";
?>
