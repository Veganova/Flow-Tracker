<?php 
// Include config file
require_once "../config/header.php";
require_once $ROOT."templates/nav_bar.php";

function getUserFromUsername($username) {
  global $pdo;

  $sql = "SELECT * FROM user WHERE username = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->setFetchMode(PDO::FETCH_OBJ); 
  if (!$stmt->execute([$username])) {
    print_r($stmt->errorInfo());
    return false;
  }
  return $stmt->fetch();
}
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if username is empty
  if(empty(trim($_POST["username"]))){
      $username_err = "Please enter username.";
  } else{
      $username = trim($_POST["username"]);
  }
  
  // Check if password is empty
  if(empty(trim($_POST["password"]))){
      $password_err = "Please enter your password.";
  } else{
      $password = trim($_POST["password"]);
  }
  
  // Validate credentials
  if(empty($username_err) && empty($password_err)){
    $userInfo = getUserFromUsername($username);
    if(password_verify($password, $userInfo->password)) {
      // Password is correct, so start a new session
      session_start();
                        
      // Store data in session variables
      $_SESSION["loggedin"] = true;
      $_SESSION["userId"] = $userInfo->id;
      $_SESSION["username"] = $username; 
      $_SESSION["email"] = $userInfo->email;                            
      
      // Redirect user to welcome page
      header("location: /index.php");
    } else {
      // Display an error message if password is not valid
      $password_err = "Invalid username/password";
    }
  }
}
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
   <?= renderNavBar("Login") ?>
    <div class="signup-container">
      <div class="signup-form">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
          <div class="form-element <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
              <div class="input-label">Username</div>
              <input type="text" name="username" maxlength="40" class="text-input plain-input" value="<?= $_POST["username"] ?? ""?>">
              <div class="help-block"><?php echo $username_err; ?></div>
          </div>    
          <div class="form-element <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
              <div class="input-label">Password</div>
              <input type="password" name="password" maxlength="200" class="text-input plain-input" value="<?= $_POST["password"] ?? "" ?>">
              <div class="help-block"><?php echo $password_err; ?></div>
          </div>
          <input type="submit" class="submit-button" value="Submit">
          <p>Don't have an account? <a href="signup.php">Sign up now</a>.</p>
        </form>
      </div>
    </div>
  </div>
</body>
</html>