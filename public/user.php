<?php
session_start();

require 'includes/dbh.inc.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PLM Navigation App - User</title>
        <link rel="stylesheet" href="user.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh;
                background-color: #f4f4f4;
            }

            .message-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100%;
                max-width: 600px;
                background-color: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                text-align: center;
            }

            .mobile .message-container {
                left:0;
                right:0;
                height: 70%;
            }

            .no-account-message {
                font-size: 1.2em;
                color: #333;
            }

            .no-account-buttons {
                display: flex;
                gap: 20px;
                margin-top: 20px;
            }

            .button {
                padding: 10px 20px;
                background-color: #007BFF;
                color: white;
                text-decoration: none;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="message-container">
            <p class="no-account-message">User not logged in.</p>
            <div class="no-account-buttons">
                <a href="index.php" class="button">Log In</a>
                <a href="home.html" class="button">Back</a>
            </div>
        </div>
    
        <script src="assets/js/mobile.js"></script>
    </body>
    </html>
    <?php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $fileTmpPath = $_FILES['profile_image']['tmp_name'];
    $fileName = $_FILES['profile_image']['name'];
    $fileSize = $_FILES['profile_image']['size'];
    $fileType = $_FILES['profile_image']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = array('jpg', 'jpeg', 'png');
    if (in_array($fileExtension, $allowedfileExtensions)) {
        $uploadFileDir = './uploads/profile_pictures/';
        $newFileName = $userId . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;

        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            try {
                $stmt = $pdo->prepare("UPDATE users SET profile_image = :profile_image WHERE user_id = :user_id");
                $stmt->bindParam(':profile_image', $dest_path, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();

                header("Location: user.php");
                exit;
            } catch (PDOException $e) {
                echo "Error updating profile image: " . $e->getMessage();
            }
        } else {
            echo "There was an error moving the uploaded file.";
        }
    } else {
        echo "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
    }
}

try {
    $stmt = $pdo->prepare("SELECT username, email, department, student_num, program, year_level, student_status, status, role, profile_image FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $user = [
            'username' => 'Unknown',
            'email' => 'N/A',
            'department' => 'N/A',
            'student_num' => 'N/A',
            'program' => 'N/A',
            'year_level' => 'N/A',
            'student_status' => 'N/A',
            'status' => 'N/A',
            'profile_image' => 'assets/front/pic.jpg'
        ];
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
    <title>PLM Navigation App - User</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="assets/dropdown.css">
    <style>

        .user-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }

        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            text-align: center;
        }

        .card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            margin-bottom: 20px;
        }
        
        .mobile .user-container, .mobile .card {
            width: 100%;
        }

        .card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .button-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            width: 100%;
        }

        .file-input {
            position: relative;
            overflow: hidden;
            margin-right: 10px;
        }

        .button, .upload-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover, .upload-button:hover {
            background-color: #0056b3;
        }

        .upload-button {
            background-color: white;
            color: #007BFF;
            border: 2px solid #007BFF;
        }

        .upload-button:hover {
            background-color: #007BFF;
            color: white;
        }

        input[type="file"] {
            display: none;
        }
    </style>
</head>
<body>
    <div class="top-ribbon">
        <div class="ribbon-button-container dropdown stay">
            <span class="ribbon-button ribbon-trigger dropView">360 VIEW</span>
            <div class="dropdown-content dropView">
                <a href="assets/tour/gv-tour.php">GV</a>
                <a href="assets/tour/gca-tour.html">GCA</a>
                <a href="assets/tour/gee-tour.html">GEE</a>
            </div>
        </div>
        <div class="ribbon-button-container dropdown">
            <span class="ribbon-button ribbon-trigger dropMenu">MENU</span>
            <div class="dropdown-content dropMenu">
                <a href="forum.php">FORUM</a>
                <a href="schedule.php">SCHEDULE</a>
                <a href="events.html">EVENTS</a>
                <a href="user.php">USER</a>
                <a href="settings.html">SETTINGS</a>
            </div>
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
        <div class="ribbon-button-container">
            <a href="settings.html" class="ribbon-button">SETTINGS</a>
        </div>
        <div class="ribbon-button-container">
            <a href="includes/logout.php" class="ribbon-button">LOGOUT</a>
        </div>
    </div>
    
    <div class="user-container">
        <h1>User Profile</h1>
        <div class="profile">
            <div class="card">
                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Picture">
                <div id="user-profile">
                    <?php
                    if (empty($user['student_num'])) {
                        echo "
                            <table>
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td>{$user['username']}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{$user['department']}</td>
                                    </tr>
                                    <tr>
                                        <th>Position</th>
                                        <td>{$user['role']}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{$user['status']}</td>
                                    </tr>
                                </tbody>
                            </table>
                        ";
                    } else {
                        echo "
                            <p id='display-name'>Name: {$user['username']}</p>
                            <p id='display-name'>ID Number: {$user['student_num']}</p>
                            <p id='display-name'>Program: {$user['program']}</p>
                            <p id='display-name'>Year Level: {$user['year_level']}</p>
                            <p id='display-name'>Status: {$user['student_status']}</p>
                        ";
                    }
                    ?>
                </div>
                <div class="button-container">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <div class="file-input">
                            <label for="input-file" class="button">Choose Image</label>
                            <input type="file" name="profile_image" id="input-file">
                        </div>
                        <button type="submit" class="upload-button">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/mobile.js"></script>
    <script src="assets/js/buttons.js"></script>
</body>
</html>