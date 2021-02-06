<?php
  require_once $_SERVER['DOCUMENT_ROOT']."/db_credentials.php";
  // The below variables at set by db_credentials.php
  // $host = 'localhost';
  // $user = 'test';
  // $password = 'test123';
  // $dbname = 'flow_tracker';

  try {
    $dsn = "mysql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
  }
?>
