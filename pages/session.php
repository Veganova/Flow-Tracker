
<!DOCTYPE html>

<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
      $ROOT = $_SERVER['DOCUMENT_ROOT'] . "/";
      // Configure DB connection
      require_once $ROOT.'config/db_connect.php';

      function addNewSession($userId) {
        global $pdo;

        $sql = "INSERT INTO session (userId) VALUES (?)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $pdo->lastInsertId();
      }

      function addQueryParameter($url, $name, $value) {
        $segmented = explode("?", $url);
        if(count($segmented) > 1) {
          $url = $segmented[0];
        }
        
        $query = $_GET;
        $query[$name] = $value;
        $query_result = http_build_query($query);

        return $url . "?" . $query_result;
      }

      $createNewSession = !isset($_GET['session']);
      if (isset($_GET['session'])) {
        $sessionId = $_GET['session'];
        $sql = 'SELECT * FROM session where id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $sessionId]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);;
        if (!$session) {
          $createNewSession = true;
        }
      }

      if ($createNewSession) {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // If no session Id provided, create a new session
        $sessionId = addNewSession(123);
        $url =  addQueryParameter($url, "session", $sessionId);
        header("Location: $url");
      }

      // Composer setup
      require_once $ROOT.'vendor/autoload.php';
      set_include_path(get_include_path() . PATH_SEPARATOR . $ROOT."vendor");
      require_once $ROOT."config/compile_styles.php";

      // Own classes
      require_once $ROOT.'classes/CategoryPill.php';
      require_once $ROOT.'classes/CategoryTimedPill.php';
      require_once $ROOT.'templates/active_session.php';
      require_once $ROOT.'component/modal.php';

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

        $sql = 'SELECT * FROM activity where sessionId=:sessionId ORDER BY endTime IS NULL, endTime ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['sessionId' => $sessionId]);
        $timedPillsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $timedPills = [];

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

      $stmt = $pdo->query('SELECT * FROM category'); # TODO where userId = **
      $categoryPillsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $categoryPills = [];
      foreach($categoryPillsRaw as $categoryPillRaw) {
        $categoryPills[] = new CategoryPill(
          $categoryPillRaw['id'], 
          $categoryPillRaw['name'], 
          $categoryPillRaw['color'],
          $categoryPillRaw['active']
        );
      }

      $timedPills = loadExistingSession($sessionId, $categoryPills);
    ?>
  </head>
  

  <body>
    <div class="container">
      <?php 
        insertCategoryTimedPillScripts();
        insertCategoryPillScripts();
        insertCategoryListScripts();
        insertModalScripts();
        
        render_top_bar($session);
        render_timed_pills($timedPills, $categoryPills);
        render_pill_choices($categoryPills);
        render_pause();
      ?>
    </div>
  </body>
</html>