<?php
session_start();
require 'dbh.inc.php';
require 'libs/PhpSpreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection settings
    $servername = "localhost";
    $username = "your_username";
    $password = "your_password";
    $dbname = "your_database";

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if file was uploaded without errors
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        
        // Load the spreadsheet file
        $spreadsheet = IOFactory::load($fileTmpPath);

        // Get the first sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Get the highest row and column numbers referenced in the sheet
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Get column headers
        $columnHeaders = $sheet->rangeToArray('A1:' . $highestColumn . '1')[0];

        // Prepare an SQL statement to insert data
        $stmt = $conn->prepare("INSERT INTO your_table (" . implode(",", $columnHeaders) . ") VALUES (" . str_repeat("?,", count($columnHeaders)-1) . "?)");
        $stmt->bind_param(str_repeat("s", count($columnHeaders)), ...$params);

        // Loop through each row of the spreadsheet
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row)[0];
            $params = $rowData;
            $stmt->execute();
        }
        
        echo "Excel file successfully uploaded and data imported into the database.";
    } else {
        echo "Error: " . $_FILES["file"]["error"];
    }

    // Close the database connection
    $conn->close();
}
?>
