<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLM Navigation App - Login</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="login-container">
        <h1>PLM Navigation App</h1>
        <form id="loginForm" action="includes/formhandler.inc.php" method="POST">

            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''; ?>" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <?php if (isset($_GET['error'])): ?>
                <div class="error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <button type="submit">Login</button>
        </form>
        
        <form action="home.html">
            <button type="submit">Log in as Guest</button>
        </form>
    </div>
</body>
</html>
