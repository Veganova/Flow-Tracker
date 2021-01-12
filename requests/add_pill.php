<?php
  include('../config/db_connect.php');
  include("../classes/CategoryTimedPill.php");

  if (isset($_POST["addPill"])) {
    $pill_id = $_POST["addPill"];
    
    // Prepared statement to prevent SQL injection
    $sql = 'SELECT * FROM category where id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'CategoryTimedPill'); 
    $stmt->execute(['id' => $pill_id]);
    
    $pill = $stmt->fetch();
    echo $pill->render();
  }
?>