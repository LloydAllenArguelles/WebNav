<!DOCTYPE html>
<html>
<head>
<title>Locating</title>
<meta charset="utf-8">
<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, minimal-ui" />
<style> @-ms-viewport { width: device-width; } </style>
<link rel="stylesheet" href="vendor/reset.min.css">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../home.css">

</head>

<body>
  <div style="position: absolute; width: 100%; height: 100%;">
    <div class="top-ribbon">
        <div class="ribbon-button-container dropdown stay">
            <span class="ribbon-button ribbon-trigger dropView">360 VIEW</span>
            <div class="dropdown-content dropView">
                <a href="gv-tour.php">GV</a>
                <a href="gca-tour.php">GCA</a>
                <a href="gee-tour.php">GEE</a>
                <a href="locator.php">Where Am I?</a>
            </div>
        </div>
        <div class="ribbon-button-container dropdown">
            <span class="ribbon-button ribbon-trigger dropMenu">MENU</span>
            <div class="dropdown-content dropMenu">
                <a href="../../home.html">HOME</a>
                <a href="../../forum.php">FORUM</a>
                <a href="../../schedule.php">SCHEDULE</a>
                <a href="../../events.html">EVENTS</a>
                <a href="../../user.php">USER</a>
                <a href="../../settings.html">SETTINGS</a>
            </div>
        </div>
        <div class="ribbon-button-container">
            <a href="../../home.html" class="ribbon-button">HOME</a>
        </div>
        <div class="ribbon-button-container">
            <a href="../../forum.php" class="ribbon-button">FORUM</a>
        </div>
        <div class="ribbon-button-container">
            <a href="../../schedule.php" class="ribbon-button">SCHEDULE</a>
        </div>
        <div class="ribbon-button-container">
            <a href="../../events.html" class="ribbon-button">EVENTS</a>
        </div>
        <div class="ribbon-button-container">
            <a href="../../user.php" class="ribbon-button">USER</a>
        </div>
        <div class="ribbon-button-container">
            <a href="../../settings.html" class="ribbon-button">SETTINGS</a>
        </div>
    </div>
<div style="position: relative; top: 0; width: 100%; height: 95%;">

<video id="camera"autoplay></video>
<button id="detect-button">  
  <img src="img/link.png" alt="Detect Closest Image">
</button>
<div id="result">
  <p id="image-name"></p>
  <img id="matched-image" src="" alt="Matched Image">
</div>

<div id="locateBar">
  <div>
    <h1 class="locateName">LOADING</h1>
  </div>
</div>

</div>
<div style="position: relative; width: 100%; height: 100%; background-color: #007bff;"></div>
</div>

<!-- Chatbot Part Start -->
<script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  chat-icon="https:&#x2F;&#x2F;assets.stickpng.com&#x2F;images&#x2F;580b57fbd9996e24bc43be12.png"
  intent="WELCOME"
  chat-title="Chatbot"
  agent-id="060d64ba-b3ff-4be9-87c6-88c97d332f18"
  language-code="en"
></df-messenger>
<!-- Chatbot Part End -->
    

<script src="vendor/screenfull.min.js" ></script>
<script src="vendor/bowser.min.js" ></script>
<script src="vendor/marzipano.js" ></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>

<script defer src="locator.js"></script>
<script src="../js/buttons.js"></script>

</body>
</html>
