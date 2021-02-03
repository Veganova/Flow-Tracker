<?php
  require_once "../config/header.php";
  if (!isLoggedIn()) {
    header("location: login.php");
  }

  require_once $ROOT."templates/nav_bar.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
    
    require_once $ROOT."config/compile_styles.php";
  ?>
</head>
<body>
  <div class="container">
    <?= renderNavBar("Profile") ?>
    <div class="signup-container">
      <div class="signup-form profile">
        <div class="profile-info-row">
          <div class="property-type">Username</div>
          <div class="property-value"><?= $_SESSION["username"] ?></div>
        </div>
        <div class="profile-info-row">
          <div class="property-type">Email</div>
          <div class="property-value"><?= $_SESSION["email"] ?></div>
        </div>
        <a href="logout.php" class="logout">Logout</a>
      </div>
    </div>
  </div>
</body>
</html>