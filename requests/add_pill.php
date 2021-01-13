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
    $stmt->execute([$session_id, $pill_category_id]);
    return $pdo->lastInsertId();
  }

  function updateActivity($U) {
    global $pdo; 

    $sql = "UPDATE users SET startTime=?, endTime=?, duration=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$U["startTime"], $U["endTime"], $U["duration"], $U["id"]]);
  }



  if (isset($_POST["addPill"])) {
    $pill_category_id = $_POST["addPill"]["categoryId"];
    $session_id = $_POST["addPill"]["sessionId"];
    
    if (isset($_POST["addPill"]["updateActivity"])) {
      // update times for previous activity
      updateActivity($_POST["addPill"]["updateActivity"]);
    }

    $category = getCategoryById($pill_category_id);
    $activityId = addNewActivity($session_id, $pill_category_id);

    $categoryPill = new CategoryTimedPill($activityId, $category->name, $category->color, "0", "0", 0);
    echo $categoryPill->render();    
  }
?>