<?php
  include('../config/db_connect.php');
  include("../templates/active_session.php");

  function toggleSaveStatus($sessionId) {
    global $pdo;

    $sql = "UPDATE session SET draft = !draft WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$sessionId]);
  }

  function getDraftStatus($sessionId) {
    global $pdo;

    $sql = "SELECT draft FROM session WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$sessionId])) {
      return $stmt->fetch(PDO::FETCH_ASSOC)["draft"];
    } else {
      return "ERROR fetching draft status";
    }
  }

  if (isset($_POST["saveSession"])) {
    $sessionId = $_POST["saveSession"]["sessionId"];

    if (!toggleSaveStatus($sessionId)) {
      echo "Error changing save status";
    } else {
      echo getDraftStatus($sessionId);
    }
  }

?>