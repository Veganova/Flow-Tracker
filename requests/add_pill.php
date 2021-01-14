<?php
  include('../config/db_connect.php');
  include("../classes/CategoryTimedPill.php");

  function getCategoryById($id) {
    global $pdo;

    // Prepared statement to prevent SQL injection
    $sql = 'SELECT * FROM category where id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_OBJ); 
    $stmt->execute(['id' => $id]);
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

    $start = (new DateTime($startTime))->format('Y-m-d H:i:s');
    $end = (new DateTime($endTime))->format('Y-m-d H:i:s');
    echo $start . "\n";
    echo $end . "\n";

    if (!$stmt->execute([$start, $end, $duration, $id])) {
      echo "Update activity failed";
    }
  }



  if (isset($_POST["addPill"])) {    
    if (isset($_POST["addPill"]["updateActivity"])) {
      // update times for previous activity
      echo "updating activity";
      $U = $_POST["addPill"]["updateActivity"];
      updateActivity($U["startTime"], $U["endTime"], $U["duration"], $U["id"]);
    }

    $pill_category_id = $_POST["addPill"]["categoryId"];
    $session_id = $_POST["addPill"]["sessionId"];
    $category = getCategoryById($pill_category_id);
    $activityId = addNewActivity($session_id, $pill_category_id);

    $categoryPill = new CategoryTimedPill($activityId, $category->name, $category->color, "0", "0", 0);
    echo $categoryPill->render();    
  }
?>