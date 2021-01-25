<?php
  $ROOT = $_SERVER['DOCUMENT_ROOT'] . "/";
  require_once $ROOT.'config/db_connect.php';
  require_once $ROOT."classes/CategoryPill.php";

  function getCategoryById($id) {
    global $pdo;

    // Prepared statement to prevent SQL injection
    $sql = 'SELECT * FROM category where id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_OBJ); 
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
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

  function editCategory($id, $name, $color) {
    global $pdo;
    
    $sql = "UPDATE category SET name=?, color=? WHERE id=?";
    $stmt= $pdo->prepare($sql);
    if (!$stmt->execute([$name, $color, $id])) {
      echo "Edit category failed";
      return false;
    }
    return true;
  }

  function deactivateCategory($id) {
    global $pdo;
    
    $sql = "UPDATE category SET active=false WHERE id=?";
    $stmt= $pdo->prepare($sql);
    if (!$stmt->execute([$id])) {
      echo "Deactivate category failed";
      return false;
    }
    return true;
  }

  if (isset($_POST['addCategory'])) {
    $color = $_POST['addCategory']['color'];
    $name = $_POST['addCategory']['name'];
    $id = addNewCategory(123, $name, $color);  

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
