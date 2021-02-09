
<?php
  function isLoggedIn() {
    return (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]);
  }

  $ROOT = $_SERVER['DOCUMENT_ROOT'] . "/";
  // Configure DB connection
  require_once $ROOT.'config/db_connect.php';


  ini_set('session.cookie_lifetime', 60 * 60 * 24 * 3);
  ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 3);
  ini_set('session.save_path', $ROOT.'/sessions');
  session_start();
?>