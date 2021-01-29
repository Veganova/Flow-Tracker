<?php
// Include config file
require_once "../config/header.php";
require_once $ROOT."templates/nav_bar.php";
 

function isExistingUsername($username) {
  global $pdo;

  $sql = "SELECT id FROM users WHERE username = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->setFetchMode(PDO::FETCH_OBJ); 
  return $stmt->execute([$username]);
}

// Data for new users
function insertDefaultCategories($userId) {
  global $pdo;

  $sql = "INSERT INTO category (userId, color, name) VALUES 
  ($userId, '#FFA34F', 'Work'), 
  ($userId, '#EF2184', 'Learn'), 
  ($userId, '#48F8ED', 'Games'), 
  ($userId, '#D4D4D4', 'Sleep')";

  $stmt = $pdo->prepare($sql);
  if ($stmt->execute([])) {
    return true;
  } 

  print_r($stmt->errorInfo());
  return false;
}

function createNewUser($username, $rawPassword, $email) {
  global $pdo;

  $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
  $sql = "INSERT INTO user (username, password, email) VALUES (?, ?, ?)";
  $stmt = $pdo->prepare($sql);
  if ($stmt->execute([$username, $hashedPassword, $email])) {
    $userId = $pdo->lastInsertId();
    return insertDefaultCategories($userId);
  } 

  print_r($stmt->errorInfo());
  return false;
}

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
  // Validate username
  if(empty(trim($_POST["username"]))){
      $username_err = "Please enter a username.";
  } else{
      $r_username = trim($_POST["username"]);
      
      if(isExistingUsername($r_username)) {
        $username_err = "This username already exists";
      } else {
        $username = htmlspecialchars($r_username);
      }
  }
  
  // Validate password
  if(empty(trim($_POST["password"]))){
      $password_err = "Please enter a password.";     
  } elseif(strlen(trim($_POST["password"])) < 6){
      $password_err = "Password must have atleast 6 characters.";
  } else{
      $password = htmlspecialchars(trim($_POST["password"]));
  }
  
  // Validate confirm password
  if(empty(trim($_POST["confirm_password"]))){
      $confirm_password_err = "Please confirm password.";     
  } else{
      $confirm_password = htmlspecialchars(trim($_POST["confirm_password"]));
      if(empty($password_err) && ($password != $confirm_password)){
        $confirm_password_err = "Password did not match.";
      }
  }
  
  if(empty(trim($_POST['email']))) {
    $email_err = "Please enter an email";
  } else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email_err = "Invalid email format";
  } else {
    $email = htmlspecialchars($_POST['email']);
  }

  // Check input errors before inserting in database
  if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){
    if (createNewUser($username, $password, $email)) {
      header("location: /index.php");
    }
  }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    require_once $ROOT."config/compile_styles.php";
  ?>
</head>
<body>
  <div class="container">
  <?= renderNavBar() ?>
    <div class="signup-container">
      <div class="signup-form">
        <div class="form-title">Sign Up</div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-element <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <div class="input-label">Username</div>
                <input type="text" name="username" maxlength="40" class="text-input plain-input" value="<?= $_POST["username"] ?? ""?>">
                <div class="help-block"><?php echo $username_err; ?></div>
            </div>    
            <div class="form-element <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <div class="input-label">Email</div>
                <input type="text" name="email" maxlength="200" class="text-input plain-input" value="<?= $_POST["email"] ?? ""?>">
                <div class="help-block"><?php echo $email_err; ?></div>
            </div>  
            <div class="form-element <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <div class="input-label">Password</div>
                <input type="password" name="password" maxlength="200" class="text-input plain-input" value="<?= $_POST["password"] ?? "" ?>">
                <div class="help-block"><?php echo $password_err; ?></div>
            </div>
            <div class="form-element <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <div class="input-label">Confirm Password</div>
                <input type="password" name="confirm_password" maxlength="200" class="text-input plain-input" value="<?= $_POST["confirm_password"] ?? "" ?>">
                <div class="help-block"><?php echo $confirm_password_err; ?></div>
            </div>

            <input type="submit" class="submit-button" value="Submit">

            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
      </div>
    </div>
  </div>    
</body>
</html>