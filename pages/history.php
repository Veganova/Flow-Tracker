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

  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $page_number = $_POST["page"] ?? 0;
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

  $sessions = getSortedSessions(0);
  function getSessionId($session) {
    return $session->id;
  }
  $sessionIds = array_map("getSessionId", $sessions);
  $activities = getActivitiesBySession($sessionIds);
?>

<pre>
<?php
  // print_r($activities);
?>
</pre>


<body>
  <div class="container">
   <?= renderNavBar() ?>
    <div class="plain-container">
      <?php
        foreach(array_reverse($sessions) as $session) {
          renderSessionDetails($session, $activities[$session->id]);  
        }
      ?>
    </div>
  </div>
</body>
</html>


