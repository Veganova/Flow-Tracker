<?php
  require_once '../config/header.php';

  require_once $ROOT."templates/active_session.php";

  if (isLoggedIn()) {
    $userId = $_SESSION["userId"];
  } else {
    exit("No user logged in!");
  }

  function toggleSaveStatus($sessionId) {
    global $pdo, $userId;

    $sql = "UPDATE session SET draft = !draft WHERE id = ? and userId = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$sessionId, $userId]);
  }

  function getDraftStatus($sessionId) {
    global $pdo, $userId;

    $sql = "SELECT draft FROM session WHERE id = ? AND userId = ?";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$sessionId, $userId])) {
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