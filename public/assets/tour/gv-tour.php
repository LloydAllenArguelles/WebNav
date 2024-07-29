<?php
session_start();

if (isset($_SESSION['user_id'])) {
  // Define JavaScript to add the 'enabled' class
  $script = "<script>
              document.addEventListener('DOMContentLoaded', function() {
                  var elements = document.querySelectorAll('.ribbon-button-container');
                  elements.forEach(function(element) {
                      element.classList.add('noguest');
                  });
              });
            </script>";

  // Output the JavaScript
  echo $script;
}

require_once '../../includes/dbh.inc.php';
?>


<!DOCTYPE html>
<html>
<head>
<title>Navigation</title>
<meta charset="utf-8">
<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, minimal-ui" />
<style> @-ms-viewport { width: device-width; } </style>
<link rel="stylesheet" href="vendor/reset.min.css">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../home.css">
<link rel="stylesheet" href="../dropdown.css">
<link rel="stylesheet" href="../hide.css">
<link rel="stylesheet" href="../manual.css">

</head>

<body class="multiple-scenes">
  <div style="position: absolute; width: 100%; height: 100%;">
    <div class="top-ribbon">
        <div class="ribbon-button-container dropdown stay">
            <span class="ribbon-button ribbon-trigger dropView">360 VIEW</span>
            <div class="dropdown-content dropView">
                <a href="gv-tour.php">GV</a>
                <a href="gca-tour.php">GCA</a>
                <a href="gee-tour.php">GEE</a>
            </div>
        </div>
        <div class="ribbon-button-container stay">
            <a href="../../home.php" class="ribbon-button">HOME</a>
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
        <div class="ribbon-button-container dropdown">
            <span class="ribbon-button ribbon-trigger dropMenu">MENU</span>
            <div class="dropdown-content dropMenu">
                <a href="../../forum.php">FORUM</a>
                <a href="../../schedule.php">SCHEDULE</a>
                <a href="../../events.html">EVENTS</a>
                <a href="../../user.php">USER</a>
                <a href="../../settings.html">SETTINGS</a>
            </div>
        </div>
        <div class="ribbon-button-container guest">
            <a href="../../index.php" class="ribbon-button">LOGIN</a>
        </div>
    </div>
    
<div style="position: relative; top: 0; width: 100%; height: 95%;">
  
<div id="pano"></div>

<div id="sceneList">
  <ul class="scenes">
    
      <a href="javascript:void(0)" class="scene" data-id="58-gv-1f-icto">
        <li class="text">GV 1F ICTO</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="59-gv-1f-nstp">
        <li class="text">GV 1F NSTP</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="60-gv-1f-ogts">
        <li class="text">GV 1F OGTS</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="61-gv-1f-office">
        <li class="text">GV 1F Office</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="0-gv-2f-left-stairs">
        <li class="text">GV 2F Left Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="1-gv-2f-com-lab-1">
        <li class="text">GV 2F Com Lab 1</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="2-gv-2f-com-lab-2">
        <li class="text">GV 2F Com Lab 2</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="3-gv-2f-center-stairs">
        <li class="text">GV 2F Center Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="4-gv-2f-rm-209">
        <li class="text">GV 2F RM 209</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="5-gv-2f-rm-208">
        <li class="text">GV 2F RM 208</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="6-gv-2f-rm-207">
        <li class="text">GV 2F RM 207</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="7-gv-2f-rm-206">
        <li class="text">GV 2F RM 206</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="8-gv-2f-restroom">
        <li class="text">GV 2F Restroom</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="9-gv-2f-rm-205">
        <li class="text">GV 2F RM 205</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="10-gv-2f-rm-204">
        <li class="text">GV 2F RM 204</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="11-gv-2f-rightmost-room">
        <li class="text">GV 2F Rightmost Room</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="12-gv-3f-left-stairs">
        <li class="text">GV 3F Left Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="13-gv-3f-accenture">
        <li class="text">GV 3F Accenture</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="14-gv-3f-rm-310">
        <li class="text">GV 3F RM 310</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="15-gv-3f-center-stairs">
        <li class="text">GV 3F Center Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="16-gv-3f-rm-309">
        <li class="text">GV 3F RM 309</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="17-gv-3f-rm-308">
        <li class="text">GV 3F RM 308</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="18-gv-3f-rm-307">
        <li class="text">GV 3F RM 307</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="19-gv-3f-rm-306">
        <li class="text">GV 3F RM 306</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="20-gv-3f-rm-305restroom">
        <li class="text">GV 3F RM 305/Restroom</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="21-gv-3f-rm-304cet">
        <li class="text">GV 3F RM 304/CET</li>
      </a>

      <a href="javascript:void(0)" class="scene" data-id="45-gv-4f-left-stairs">
        <li class="text">GV 4F Left Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="46-gv-4f-com-lab-3--4">
        <li class="text">GV 4F Com Lab 3 &amp; 4</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="47-gv-5f-drawing-room">
        <li class="text">GV 5F Drawing Room</li>
      </a>
  </ul>
</div>

<div id="targetList">
  <ul class="targets">
      <a href="javascript:void(0)" class="target" data-id="58-gv-1f-icto">
        <li class="text">GV 1F ICTO</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="59-gv-1f-nstp">
        <li class="text">GV 1F NSTP</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="60-gv-1f-ogts">
        <li class="text">GV 1F OGTS</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="61-gv-1f-office">
        <li class="text">GV 1F Office</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="0-gv-2f-left-stairs">
        <li class="text">GV 2F Left Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="1-gv-2f-com-lab-1">
        <li class="text">GV 2F Com Lab 1</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="2-gv-2f-com-lab-2">
        <li class="text">GV 2F Com Lab 2</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="3-gv-2f-center-stairs">
        <li class="text">GV 2F Center Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="4-gv-2f-rm-209">
        <li class="text">GV 2F RM 209</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="5-gv-2f-rm-208">
        <li class="text">GV 2F RM 208</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="6-gv-2f-rm-207">
        <li class="text">GV 2F RM 207</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="7-gv-2f-rm-206">
        <li class="text">GV 2F RM 206</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="8-gv-2f-restroom">
        <li class="text">GV 2F Restroom</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="9-gv-2f-rm-205">
        <li class="text">GV 2F RM 205</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="10-gv-2f-rm-204">
        <li class="text">GV 2F RM 204</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="11-gv-2f-rightmost-room">
        <li class="text">GV 2F Rightmost Room</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="12-gv-3f-left-stairs">
        <li class="text">GV 3F Left Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="13-gv-3f-accenture">
        <li class="text">GV 3F Accenture</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="14-gv-3f-rm-310">
        <li class="text">GV 3F RM 310</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="15-gv-3f-center-stairs">
        <li class="text">GV 3F Center Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="16-gv-3f-rm-309">
        <li class="text">GV 3F RM 309</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="17-gv-3f-rm-308">
        <li class="text">GV 3F RM 308</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="18-gv-3f-rm-307">
        <li class="text">GV 3F RM 307</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="19-gv-3f-rm-306">
        <li class="text">GV 3F RM 306</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="20-gv-3f-rm-305restroom">
        <li class="text">GV 3F RM 305/Restroom</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="21-gv-3f-rm-304cet">
        <li class="text">GV 3F RM 304/CET</li>
      </a>

      <a href="javascript:void(0)" class="target" data-id="45-gv-4f-left-stairs">
        <li class="text">GV 4F Left Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="46-gv-4f-com-lab-3--4">
        <li class="text">GV 4F Com Lab 3 &amp; 4</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="47-gv-5f-drawing-room">
        <li class="text">GV 5F Drawing Room</li>
      </a>
  </ul>
</div>

<div class="fromto" style="top:40px">
  <h1>Current: </h1>
</div>

<div class="fromto" style="top:0">
  <h1>Go to: </h1>
</div>

<div id="titleBar">
  <div>
    <h1 class="sceneName"></h1>
  </div>
</div>

<div id="targetBar">
  <div>
    <h1 class="targetName">Press the button on the left.</h1>
  </div>
</div>

<!-- Manual Part Start -->
<div id="userManual">
  <div class="manual-tile-container">
    <div class="manual-tile">
      <img src="../img/ins-full.png">
      <div class=>
        <h5>FULL SCREEN BUTTON</h5>
        <h1>Click to enter Fullscreen</h1>
      </div>
    </div>
    <div class="manual-tile">
      <img src="../img/ins-motion.png">
      <div>
        <h5>STOP IDLE MOTION BUTTON</h5>
        <h1>Click to stop Idle Motion Effect</h1>
      </div>
    </div>
    <div class="manual-tile">
      <img src="../img/ins-arrowneutral.png">
      <div>
        <h5>DIRECTIONAL ARROW</h5>
        <h1>Arrow towards Pointed Direction</h1>
      </div>
    </div>
    <div class="manual-tile">
      <img src="../img/ins-arrowgo.png">
      <div>
        <h5>NAVIGATION ARROW</h5>
        <h1>Arrow to Destination Path</h1>
      </div>
    </div>
    <div class="manual-tile">
      <img src="../img/ins-arrowno.png">
      <div>
        <h5>BACK ARROW</h5>
        <h1>Arrow to Previous Path</h1>
      </div>
    </div>
    <div class="manual-tile ">
      <img src="../img/ins-legend.png">
      <div>
        <h5>LEGEND</h5>
        <h1>Open Legend Window</h1>
      </div>
    </div>

  </div>
  <div class="manual-img">
    <img src=../img/ins-truction.png>
  </div>
</div>
<!-- Manual Part End -->


<a href="javascript:void(0)" id="autorotateToggle">
  <img class="icon off" src="img/play.png">
  <img class="icon on" src="img/pause.png">
</a>

<a href="javascript:void(0)" id="fullscreenToggle">
  <img class="icon off" src="img/fullscreen.png">
  <img class="icon on" src="img/windowed.png">
</a>

<a href="javascript:void(0)" id="legendToggle">
  <img class="icon off" src="img/legend.png">
  <img class="icon on" src="img/close.png">
</a>

<a href="javascript:void(0)" id="sceneListToggle">
  <img class="icon off" src="img/down.png">
  <img class="icon on" src="img/up.png">
</a>

<a href="javascript:void(0)" id="targetListToggle">
  <img class="icon off" src="img/down.png">
  <img class="icon on" src="img/up.png">
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

<script>
     var userId = "<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>";
</script>
    
<script src="vendor/screenfull.min.js" ></script>
<script src="vendor/bowser.min.js" ></script>
<script src="vendor/marzipano.js" ></script>

<script src="gv-data.js"></script>
<script src="index.js"></script>
<script src="../js/buttons.js"></script>

</body>
</html>

