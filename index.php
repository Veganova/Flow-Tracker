
<?php
  // Initialize the session
  require_once "config/header.php";
  require_once $ROOT."templates/nav_bar.php";
?>

<!DOCTYPE html>

<html>
  <head>
    <?php
      require_once $ROOT."config/compile_styles.php";

      if (isLoggedIn()) {
        require_once $ROOT.'templates/session_card.php';
        require_once $ROOT."functions/history.php";
    
        $userId = $_SESSION['userId'];
        $page_number = 0;
        $ELEMENTS_PER_PAGE = 6;
    
        if($_SERVER["REQUEST_METHOD"] == "GET") {
          $page_number = $_GET["page"] ?? 0;
        }
    
        $sessions = getSortedSessions($page_number, $userId, $ELEMENTS_PER_PAGE);
        function getSessionId($session) {
          return $session->id;
        }
        $sessionIds = array_map("getSessionId", $sessions);
        $activities = count($sessionIds) == 0 ? [] : getActivitiesBySession($sessionIds);
        $totalActivityCount = getActivityCount($userId);
        $numPages = ceil($totalActivityCount / $ELEMENTS_PER_PAGE);
      }
    ?>
  </head>
  

  <body>
    <div class="container">
      <?= renderNavBar("Home") ?>
      <div class="plain-container"> 
        <?php if(isLoggedIn()) {?>
          <div class="card-panel-container">
            <div class="section-title">Your sessions</div>
            <div class="card-panel scrolling-container">

              <?php foreach($sessions as $session) {
                renderSessionDetailsCard($session, $activities[$session->id]);  
              }
              ?>
              <a class="bg-button new-session" href="/pages/session.php">+ <span>New Session</span></a>
          </div>
        <?php } ?>
      </div>
    </div>
  </body>
</html>