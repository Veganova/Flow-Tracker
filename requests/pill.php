<?php
  $ROOT = $_SERVER['DOCUMENT_ROOT'] . "/";
  require_once $ROOT.'config/db_connect.php';
  require_once $ROOT."classes/CategoryTimedPill.php";

  function getCategoryById($id) {
    global $pdo;

    // Prepared statement to prevent SQL injection
    $sql = 'SELECT * FROM category where id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_OBJ); 
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
  }

  function removeActivity($id) {
    global $pdo;

    // Prepared statement to prevent SQL injection
    $sql = 'DELETE FROM activity where id = :id';
    $stmt = $pdo->prepare($sql);
    if (!$stmt->execute(['id' => $id])) {
      echo "Failed to remove activity";
      return false;
    }
    return true;
  }

  function getCategoryByActivityId($id) {
    global $pdo;

    // Prepared statement to prevent SQL injection
    $sql = 'SELECT * FROM category where id = (SELECT categoryId FROM activity where id = :id)';
    $stmt = $pdo->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_OBJ); 
    if (!$stmt->execute(['id' => $id])) {
      echo "Failed to get id";
    }
    return $stmt->fetch();
  }

  // Returns the id of new activity
  function addNewActivity($session_id, $pill_category_id) {
    global $pdo;
    
    $sql = "INSERT INTO activity (sessionId, categoryId, duration) VALUES (?, ?, 0)";
    $stmt= $pdo->prepare($sql);
    if (!$stmt->execute([$session_id, $pill_category_id])) {
      echo "Add new activity failed";
    }
    return $pdo->lastInsertId();
  }

  function updateActivity($startTime, $endTime, $duration, $id) {
    global $pdo; 

    $sql = "UPDATE activity SET startTime=?, endTime=?, duration=? WHERE id=?";
    $stmt= $pdo->prepare($sql);

    if (!$stmt->execute([$startTime, $endTime, $duration, $id])) {
      echo "Update activity failed";
    }
  }

  if (isset($_POST["updateActivity"])) {
    // update times for previous activity
    $U = $_POST["updateActivity"];
    $startTime = (new DateTime($U["startTime"]))->format('Y-m-d H:i:s');
    $endTime = (new DateTime($U["endTime"]))->format('Y-m-d H:i:s');

    updateActivity($startTime, $endTime, $U["duration"], $U["id"]);
    
    // $category = getCategoryByActivityId($U["id"]);
    // $activity = new CategoryTimedPill(
    //   $U["id"], 
    //   $category->name, 
    //   $category->color, 
    //   $startTime, 
    //   $endTime, 
    //   $U["duration"]
    // );
    // echo $activity->render();    
  }

  if (isset($_POST["addPill"])) {    
    $pill_category_id = $_POST["addPill"]["categoryId"];
    $session_id = $_POST["addPill"]["sessionId"];
    $category = getCategoryById($pill_category_id);
    $activityId = addNewActivity($session_id, $pill_category_id);

    $activity = new CategoryTimedPill($activityId, $category->name, $category->color, "0", "0", 0);
    echo $activity->render();    
  }

  if (isset($_POST["removeActivity"])) {    
    $activityId = $_POST["removeActivity"]["id"];
    echo removeActivity($activityId);
  }

  
?>