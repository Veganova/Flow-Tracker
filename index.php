
<!DOCTYPE html>

<html>
<head>
<?php

  require __DIR__.'/vendor/autoload.php';

  // set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__."/vendor");

  $directory = "styles";
  require_once "scssphp/scssphp/scss.inc.php";
  // require_once "scssphp/scss.inc.php";
  // require "scssphp/scss.inc.php";
  use ScssPhp\ScssPhp\Compiler;

  $scss = new Compiler();
  $scss->setImportPaths('styles');
  
  // will search for 'assets/stylesheets/mixins.scss'
?>

<style>
  <?= $scss->compile('@import "container.scss";'); ?>
</style>
</head>
<?php

  require_once 'config/classes.php';
  
  include('config/db_connect.php');
  
  $stmt = $pdo->query('SELECT * FROM category');
  $cats = $stmt->fetchAll(PDO::FETCH_CLASS, "Category");
  foreach($cats as $cat) {
    echo $cat->bigName().'<br>';
  }
  // while($row = $stmt->fetch()) {
  //   echo $row->name . '<br>';
  // }
?>

<body>
  <div class="container">
    Container
  </div>
</body>
</html>