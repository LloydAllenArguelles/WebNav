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

<body class="multiple-scenes">
  <div style="position: absolute; width: 100%; height: 100%;">
  <div class="top-ribbon">
    <div class="ribbon-button-container dropdown">
      <span class="ribbon-button ribbon-trigger">360 VIEW</span>
      <div class="dropdown-content">
            <a href="gv-tour.html">GV</a>
            <a href="gca-tour.html">GCA</a>
            <a href="gee-tour.html">GEE</a>
            <a href="locator.php">Where am I?</a>
      </div>
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

<div id="titleBar">
  <div>
    <h1 class="sceneName">BLANK</h1>
  </div>
</div>

<div id="targetBar">
  <div>
    <h1 class="targetName">Press the button on the left.</h1>
  </div>
</div>

<div class="fromto" style="bottom:40px">
  <h1>Current: </h1>
</div>

<div class="fromto" style="bottom:0">
  <h1>Go to: </h1>
</div>

<a href="javascript:void(0)" id="autorotateToggle">
  <img class="icon off" src="img/play.png">
  <img class="icon on" src="img/pause.png">
</a>

<a href="javascript:void(0)" id="fullscreenToggle">
  <img class="icon off" src="img/fullscreen.png">
  <img class="icon on" src="img/windowed.png">
</a>

<a href="javascript:void(0)" id="sceneListToggle">
  <img class="icon off" src="img/up.png">
  <img class="icon on" src="img/down.png">
</a>

<a href="javascript:void(0)" id="targetListToggle">
  <img class="icon off" src="img/up.png">
  <img class="icon on" src="img/down.png">
</a>

<a href="javascript:void(0)" id="viewUp" class="viewControlButton viewControlButton-1">
  <img class="icon" src="img/up.png">
</a>
<a href="javascript:void(0)" id="viewDown" class="viewControlButton viewControlButton-2">
  <img class="icon" src="img/down.png">
</a>
<a href="javascript:void(0)" id="viewLeft" class="viewControlButton viewControlButton-3">
  <img class="icon" src="img/left.png">
</a>
<a href="javascript:void(0)" id="viewRight" class="viewControlButton viewControlButton-4">
  <img class="icon" src="img/right.png">
</a>
<a href="javascript:void(0)" id="viewIn" class="viewControlButton viewControlButton-5">
  <img class="icon" src="img/plus.png">
</a>
<a href="javascript:void(0)" id="viewOut" class="viewControlButton viewControlButton-6">
  <img class="icon" src="img/minus.png">
</a>
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
<script src="gee-data.js"></script>
<script src="index.js"></script>

</body>
</html>
