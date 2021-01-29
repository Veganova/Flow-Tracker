
<?php
  function isLoggedIn() {
    return (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]);
  }

  session_start();

  $ROOT = $_SERVER['DOCUMENT_ROOT'] . "/";
  // Configure DB connection
  require_once $ROOT.'config/db_connect.php';
?>