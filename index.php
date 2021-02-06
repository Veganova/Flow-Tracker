
<?php
  // Initialize the session
  require_once "config/header.php";
  
  require_once "templates/nav_bar.php";
?>

<!DOCTYPE html>

<html>
  <head>
    <?php
      require_once $ROOT."config/compile_styles.php";
    ?>
  </head>
  

  <body>
    <div class="container">
      <?= renderNavBar("Home") ?>

      <?php if(isLoggedIn()) { ?>
        <div class="plain-container">
          <a class="bg-button new-session" href="/pages/session.php">+ <span>New Session</span></a>
        </div>
      <?php } ?>
     
    </div>
  </body>
</html>