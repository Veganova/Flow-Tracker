<?php
  require_once '../config/header.php';

  if (!isLoggedIn()) {
    header("location: login.php");
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    require_once $ROOT."config/compile_styles.php";
  ?>
</head>

<?php
  require_once $ROOT."templates/nav_bar.php";
  require_once $ROOT.'templates/session_card.php';
  require_once $ROOT."functions/history.php";

  $userId = $_SESSION['userId'];
  $page_number = 0;
  $ELEMENTS_PER_PAGE = 10;

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
?>


<body>
  <div class="container">
    <?= renderNavBar("History") ?>
    <div class="plain-container">
      <?php
        renderTableColumnNames(["Last Updated", "Duration", "#Activities", "Breakdown"]);
        foreach($sessions as $session) {
          renderSessionDetailsRow($session, $activities[$session->id]);  
        }
      ?>
      <?php if($numPages > 1) { ?>
      <form class="page-numbers" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
        <?php
          foreach(range(0, $numPages - 1) as $numPage) {
        ?>
          <button 
            value="<?= $numPage ?>" 
            name="page" 
            type="submit"
            <?= $numPage == $page_number ? 'disabled' : '' ?> 
            class="page-number border-button <?= $numPage == $page_number ? 'border-button--active' : '' ?>"
          >
            <?= $numPage + 1 ?>
          </button>
        <?php
          }
        }
        ?>
      </form>
    </div>
  </div>
</body>
</html>


