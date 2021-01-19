<?php
  include('../config/db_connect.php');
  include("../classes/CategoryPill.php");

  function getCategoryById($id) {
    global $pdo;

    // Prepared statement to prevent SQL injection
    $sql = 'SELECT * FROM category where id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_OBJ); 
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
  }

  function addNewSession($userId) {
    global $pdo;

    $sql = "INSERT INTO session (userId) VALUES (?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $pdo->lastInsertId();
  }

  // Returns the id of new category
  function addNewCategory($userId, $name, $color) {
    global $pdo;
    
    $sql = "INSERT INTO category (userId, name, color) VALUES (?, ?, ?)";
    $stmt= $pdo->prepare($sql);
    if (!$stmt->execute([$userId, $name, $color])) {
      echo "Add new category failed";
    }
    return $pdo->lastInsertId();
  }

  if (isset($_POST['addCategory'])) {
    $color = $_POST['addCategory']['color'];
    $name = $_POST['addCategory']['name'];
    $id = addNewCategory(123, $name, $color);  

    $addedCategory = new CategoryPill($id, $name, $color);
    echo $addedCategory->render();
  }

?>
