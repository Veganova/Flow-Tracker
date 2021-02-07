<?php
  function renderNavBar($title="") {
    global $ROOT;
?>
  <div class="nav-container">
    <div class="nav-bar">
        <a href="/" class="page-name">FTT</a>
        <span class="filler"></span>
<?php 
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
?>
          <a href="/pages/history.php" class="nav-el nav-img">
            <?= file_get_contents($ROOT."assets/history.svg"); ?>
          </a>
          <a href="/pages/profile.php" class="nav-el nav-img profile-icon">
            <?= file_get_contents($ROOT."assets/profile.svg"); ?>
          </a>
<?php
        } else {
?>
        <a class="nav-el border-button" href="/pages/login.php">Login</a>
        <a class="nav-el border-button" href="/pages/signup.php">Sign Up</a>
<?php
        }
?>
    </div>
    <div class="page-title"><?= $title ?></div>
  </div>
<?php
  }
?>