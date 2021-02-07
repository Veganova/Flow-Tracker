<?php

function getActivityCount($userId) {
  global $pdo;
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM session WHERE userId = ?");
  if (!$stmt->execute([$userId])) {
    print_r($stmt->errorInfo());
  }
  return $stmt->fetchColumn(); 
}

function getSortedSessions($page, $userId, $elementsPerPage) {
  global $pdo;
  $stmt = $pdo->prepare("
    SELECT * FROM session 
    WHERE userId = :userId
    ORDER BY updated_at DESC
    LIMIT :elementNumber, :countPerPage
  ");
  $elementNumber = ($page * $elementsPerPage);
  $stmt->bindParam(':userId',  $userId, PDO::PARAM_INT);
  $stmt->bindParam(':elementNumber',  $elementNumber, PDO::PARAM_INT);
  $stmt->bindParam(':countPerPage', $elementsPerPage, PDO::PARAM_INT);
  if (!$stmt->execute()) {
    print_r($stmt->errorInfo());
  }
  return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function getActivitiesBySession($sessionIds) {
  global $pdo; 
  $idsStr = implode(',', $sessionIds);
  $stmt = $pdo->prepare("
  SELECT * from activity 
  JOIN category ON activity.categoryId = category.id
  WHERE sessionId in (".$idsStr.")
  ORDER BY endTime IS NULL, endTime ASC
  ");
  if (!$stmt->execute()) {
    print_r($stmt->errorInfo());
  }

  $activities = $stmt->fetchAll(PDO::FETCH_OBJ);
  $bySession = [];

  foreach($sessionIds as $sessionId) {
    $bySession[$sessionId] = [];
  }
  
  foreach($activities as $activity) {
    array_push($bySession[$activity->sessionId], $activity);
  } 

  return $bySession;
}

?>