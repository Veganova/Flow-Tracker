
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
    ?>
  </head>
  

  <body>
    <div class="container">
      <a href="/session.php">New Session </a>
    </div>
  </body>
</html>