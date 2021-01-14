
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
          $url = $segmented[1];
        }
        
        $query = $_GET;
        $query[$name] = $value;
        $query_result = http_build_query($query);

        return $url . "?" . $query_result;
      }

      if (!isset($_GET['session'])) {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // If no session Id provided, create a new session
        $sessionId = addNewSession(123);
        $url =  addQueryParameter($url, "session", $sessionId);
        header("Location: $url");
      }

      // Own classes
      require_once 'classes/CategoryPill.php';
      require_once 'classes/CategoryTimedPill.php';
      require_once 'templates/category_list.php';

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

      // echo $url;
      $sessionId = $_GET['session'];
      $stmt = $pdo->query('SELECT * FROM category'); # TODO where userId = **
      $categoryPills = $stmt->fetchAll(PDO::FETCH_CLASS, "CategoryPill");
      $timedPills = loadExistingSession($sessionId, $categoryPills);
    ?>
  </head>
  

  <body>
    <div class="container">
      <?php 
        render_timed_pills($timedPills, $categoryPills);
        render_pill_choices($categoryPills);
        render_pause();
      ?>
    </div>
  </body>
</html>

