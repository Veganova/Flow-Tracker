<?php
  function renderNavBar() {
    global $ROOT;
?>
    <div class="nav-bar">
        <a href="/" class="page-name">FT</a>
        <span class="filler"></span>
        <a href="/" class="nav-el nav-img">
          <?= file_get_contents($ROOT."assets/history.svg"); ?>
        </a>
<?php 
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
?>
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
<?php
  }
?>