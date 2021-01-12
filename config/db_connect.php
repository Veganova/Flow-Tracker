<?php
  $host = 'localhost';
  $user = 'test';
  $password = 'test123';
  $dbname = 'flow_tracker';

  $dsn = "mysql:host=$host;dbname=$dbname";

  $pdo = new PDO($dsn, $user, $password);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS);
?>