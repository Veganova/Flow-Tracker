<?php
  require_once '../config/header.php';

  if (!isLoggedIn()) {
    header("location: login.php");
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
    require_once $ROOT."config/compile_styles.php";
  ?>
</head>

<?
  require_once $ROOT."templates/nav_bar.php";
  require_once $ROOT.'templates/session_card.php';

  $userId = $_SESSION['userId'];
  $page_number = 0;
  $ELEMENTS_PER_PAGE = 10;

  if($_SERVER["REQUEST_METHOD"] == "GET") {
    $page_number = $_GET["page"] ?? 0;
  }

  function getActivityCount() {
    global $pdo, $userId, $ELEMENTS_PER_PAGE;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM session WHERE userId = ?");
    if (!$stmt->execute([$userId])) {
      print_r($stmt->errorInfo());
    }
    return $stmt->fetchColumn(); 
  }

  function getSortedSessions($page) {
    global $pdo, $userId, $ELEMENTS_PER_PAGE;
    $stmt = $pdo->prepare("
      SELECT * FROM session 
      WHERE userId = :userId
      ORDER BY updated_at DESC
      LIMIT :elementNumber, :countPerPage
    ");
    $elementNumber = ($page * $ELEMENTS_PER_PAGE);
    $stmt->bindParam(':userId',  $userId, PDO::PARAM_INT);
    $stmt->bindParam(':elementNumber',  $elementNumber, PDO::PARAM_INT);
    $stmt->bindParam(':countPerPage', $ELEMENTS_PER_PAGE, PDO::PARAM_INT);
    if (!$stmt->execute()) {
      print_r($stmt->errorInfo());
    }
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }
  
  function getActivitiesBySession($sessionIds) {
    global $pdo; 
    $idsStr = implode(',', $sessionIds);
    $stmt = $pdo->prepare("
    SELECT * from activity 
    JOIN category ON activity.categoryId = category.id
    WHERE sessionId in (".$idsStr.")
    ORDER BY endTime IS NULL, endTime ASC
    ");
    if (!$stmt->execute()) {
      print_r($stmt->errorInfo());
    }

    $activities = $stmt->fetchAll(PDO::FETCH_OBJ);
    $bySession = [];

    foreach($sessionIds as $sessionId) {
      $bySession[$sessionId] = [];
    }
    
    foreach($activities as $activity) {
      array_push($bySession[$activity->sessionId], $activity);
    } 

    return $bySession;
  }

  $sessions = getSortedSessions($page_number);
  function getSessionId($session) {
    return $session->id;
  }
  $sessionIds = array_map("getSessionId", $sessions);
  $activities = count($sessionIds) == 0 ? [] : getActivitiesBySession($sessionIds);
  $totalActivityCount = getActivityCount();
  $numPages = ceil($totalActivityCount / $ELEMENTS_PER_PAGE);
?>


<body>
  <div class="container">
   <?= renderNavBar("History") ?>
    <div class="plain-container">
      <?php
        renderTableColumnNames(["Last Updated", "Duration", "#Activities", "Breakdown"]);
        foreach($sessions as $session) {
          renderSessionDetails($session, $activities[$session->id]);  
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


