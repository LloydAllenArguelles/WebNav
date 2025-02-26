<?php
session_start();
require_once '../../includes/dbh.inc.php';
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
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
            <a href="../../events.php" class="ribbon-button">EVENTS</a>
        </div>
        <div class="ribbon-button-container">
            <a href="../../settings.php" class="ribbon-button">SETTINGS</a>
        </div>
        <div class="ribbon-button-container dropdown">
            <span class="ribbon-button ribbon-trigger dropMenu">MENU</span>
            <div class="dropdown-content dropMenu">
                <a href="../../forum.php">FORUM</a>
                <a href="../../schedule.php">SCHEDULE</a>
                <a href="../../events.php">EVENTS</a>
                <a href="../../settings.php">SETTINGS</a>
            </div>
        </div>
        <div class="ribbon-button-container guest">
            <a href="../../index.php" class="ribbon-button">LOGIN</a>
        </div>
        <div class="ribbon-button-container dropdown">
            <?php echo 
            "<a href='../../user.php' class='ribbon-button'>USER: {$user['username']}</a>"
            ?>
        </div>
    </div>

<div style="position: relative; top: 0; width: 100%; height: 95%;">
  
<div id="pano"></div>

<div id="sceneList">
  <ul class="scenes">
    
      <a href="javascript:void(0)" class="scene" data-id="48-gca-outside">
        <li class="text">GCA Outside</li>
      </a>
        
      <a href="javascript:void(0)" class="scene" data-id="22-gca-1f-center-stairs">
        <li class="text">GCA 1F Center Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="23-gca-2f-center-stairs">
        <li class="text">GCA 2F Center Stairs</li>
      </a>    

      <a href="javascript:void(0)" class="scene" data-id="24-gca-2f-avr2a">
        <li class="text">GCA 2F AVR2A</li>
      </a>

      <a href="javascript:void(0)" class="scene" data-id="49-gca-2f-avr2b">
        <li class="text">GCA 2F AVR2B</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="50-gca-2f-avr1b">
        <li class="text">GCA 2F AVR1B</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="51-gca-2f-avr1a">
        <li class="text">GCA 2F AVR1A</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="52-gca-3f-rm-305">
        <li class="text">GCA 3F RM 305</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="53-gca-3f-rm-304">
        <li class="text">GCA 3F RM 304</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="54-gca-3f-rm-303">
        <li class="text">GCA 3F RM 303</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="55-gca-3f-rm-302">
        <li class="text">GCA 3F RM 302</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="56-gca-3f-rm-301b">
        <li class="text">GCA 3F RM 301B</li>
      </a>
    
      <a href="javascript:void(0)" class="scene" data-id="57-gca-3f-rm-301a">
        <li class="text">GCA 3F RM 301A</li>

      </a>
  </ul>
</div>

<div id="targetList">
  <ul class="targets">
    
      <a href="javascript:void(0)" class="target" data-id="48-gca-outside">
        <li class="text">GCA Outside</li>
      </a>
        
      <a href="javascript:void(0)" class="target" data-id="22-gca-1f-center-stairs">
        <li class="text">GCA 1F Center Stairs</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="23-gca-2f-center-stairs">
        <li class="text">GCA 2F Center Stairs</li>
      </a>    

      <a href="javascript:void(0)" class="target" data-id="24-gca-2f-avr2a">
        <li class="text">GCA 2F AVR2A</li>
      </a>

      <a href="javascript:void(0)" class="target" data-id="49-gca-2f-avr2b">
        <li class="text">GCA 2F AVR2B</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="50-gca-2f-avr1b">
        <li class="text">GCA 2F AVR1B</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="51-gca-2f-avr1a">
        <li class="text">GCA 2F AVR1A</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="52-gca-3f-rm-305">
        <li class="text">GCA 3F RM 305</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="53-gca-3f-rm-304">
        <li class="text">GCA 3F RM 304</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="54-gca-3f-rm-303">
        <li class="text">GCA 3F RM 303</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="55-gca-3f-rm-302">
        <li class="text">GCA 3F RM 302</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="56-gca-3f-rm-301b">
        <li class="text">GCA 3F RM 301B</li>
      </a>
    
      <a href="javascript:void(0)" class="target" data-id="57-gca-3f-rm-301a">
        <li class="text">GCA 3F RM 301A</li>
      </a>
  </ul>
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

<div id="targetNotify">
  <h1>You have reached your destination.</h1>
</div>

<div class="fromto" style="top:40px">
  <h1>Current: </h1>
</div>

<div class="fromto" style="top:0">
  <h1>Go to: </h1>
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
  chat-icon="../../uploads/profile_pictures/1.png"
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

<script src="gca-data.js"></script>
<script src="index.js"></script>
<script src="../js/buttons.js"></script>

</body>
</html>
