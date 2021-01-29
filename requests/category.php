<?php
  require_once '../config/header.php';

  require_once $ROOT."classes/CategoryPill.php";

  if (isLoggedIn()) {
    $userId = $_SESSION["userId"];
  } else {
    exit("No user logged in!");
  }

  function getCategoryById($id) {
    global $pdo, $userId;

    // Prepared statement to prevent SQL injection
    $sql = 'SELECT * FROM category where id = :id AND userId = :userId';
    $stmt = $pdo->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_OBJ); 
    $stmt->execute(['id' => $id, 'userId' => $userId]);
    return $stmt->fetch();
  }

  // Returns the id of new category
  function addNewCategory($name, $color) {
    global $pdo, $userId;
    
    $sql = "INSERT INTO category (userId, name, color) VALUES (?, ?, ?)";
    $stmt= $pdo->prepare($sql);
    if (!$stmt->execute([$userId, $name, $color])) {
      print_r($stmt->errorInfo());
      echo "Add new category failed";
    }
    return $pdo->lastInsertId();
  }

  function editCategory($id, $name, $color) {
    global $pdo, $userId;
    
    $sql = "UPDATE category SET name=?, color=? WHERE id=? AND userId=?";
    $stmt= $pdo->prepare($sql);
    if (!$stmt->execute([$name, $color, $id, $userId])) {
      print_r($stmt->errorInfo());
      echo "Edit category failed";
      return false;
    }
    return true;
  }

  function deactivateCategory($id) {
    global $pdo, $userId;
    
    $sql = "UPDATE category SET active=false WHERE id=? AND userId=?";
    $stmt= $pdo->prepare($sql);
    if (!$stmt->execute([$id, $userId])) {
      print_r($stmt->errorInfo());
      echo "Deactivate category failed";
      return false;
    }
    return true;
  }

  if (isset($_POST['addCategory'])) {
    $color = $_POST['addCategory']['color'];
    $name = $_POST['addCategory']['name'];
    $id = addNewCategory($name, $color);  

    $addedCategory = new CategoryPill($id, $name, $color);
    echo $addedCategory->render();
  }

  if (isset($_POST['editCategory'])) {
    $color = $_POST['editCategory']['color'];
    $name = $_POST['editCategory']['name'];
    $id = $_POST['editCategory']['id'];  

    if (editCategory($id, $name, $color)) {
      $editedCategory = new CategoryPill($id, $name, $color);
      echo $editedCategory->render();
    }
  }

  if (isset($_POST['removeCategory'])) {
    $id = $_POST['removeCategory']['id'];  

    if (deactivateCategory($id)) {
      echo true;
    } else {
      echo false;
    }
  }
?>
