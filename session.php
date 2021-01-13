
<!DOCTYPE html>

<html>
  <head>
    <?php
        // Composer setup
        require_once __DIR__.'/vendor/autoload.php';
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__."/vendor");
        include("config/compile_styles.php");

        // Configure DB connection
        include('config/db_connect.php');

        // Own classes
        require_once 'classes/CategoryPill.php';
        require_once 'classes/CategoryTimedPill.php';
        require_once 'templates/category_list.php';
    ?>
  </head>

  <?php

    function findCategoryById($categoryId, $categoryPills) {
      foreach($categoryPills as $categoryPill) {
        if ($categoryPill->id == $categoryId) {
          return $categoryPill;
        }
      }

      return null;
    }

    function loadExistingSession($sessionId, $categoryPills) {
      global $pdo;

      echo "LOADING EXISTING!!";
      $sql = 'SELECT * FROM activity where sessionId = :sessionId';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['sessionId' => $sessionId]);
      $timedPillsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach($timedPillsRaw as $timedPillRaw) {
        // $key = array_search($timedPillRaw['categoryId'], array_column($categoryPills, 'id'));
        $categoryPill = findCategoryById($timedPillRaw['categoryId'], $categoryPills);
        $timedPills[] = new CategoryTimedPill(
          $timedPillRaw['id'], 
          $categoryPill->name, 
          $categoryPill->color, 
          $timedPillRaw['startTime'], 
          $timedPillRaw['endTime'], 
          $timedPillRaw['duration']
        );
      }
      return $timedPills;
    }

    function addQueryParameter($name, $value) {
      global $url;

      $separator = (parse_url($url, PHP_URL_QUERY) ? '&' : '?');
      $url .=  $separator . "$name=$value";
    }

    $timedPills = [];
    $stmt = $pdo->query('SELECT * FROM category'); # TODO where userId = **
    $categoryPills = $stmt->fetchAll(PDO::FETCH_CLASS, "CategoryPill");

    if (isset($_GET['sessionId'])) {
      $timedPills = loadExistingSession($sessionId, $categoryPills);
    } else {
      // If no session Id provided, create a new session
      $sql = "INSERT INTO session (userId) VALUES ('test123')";
      $sessionId = $pdo->lastInsertId();
      addQueryParameter("session", $sessionId);
    }
  ?>
  

  <body>
    <div class="container">
      <?php 
        // render_timed_pills($timedPills, $categoryPills);
        // render_pill_choices($categoryPills);
        // render_pause();
      ?>
    </div>
  </body>
</html>

