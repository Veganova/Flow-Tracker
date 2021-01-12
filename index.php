
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
        require_once 'templates/category_list.php';
    ?>
  </head>
  

  <body>
    <div class="container">
      <?php 
        render_timed_pills(0);
        render_pill_choices();
      ?>
    </div>
  </body>
</html>