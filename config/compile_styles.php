<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<?php
  require_once "scssphp/scssphp/scss.inc.php";
  use ScssPhp\ScssPhp\Compiler;
  $scss = new Compiler();
  $scss->setImportPaths('styles');
?>

<style>
  <?= $scss->compile('@import "container.scss";'); ?>
  <?= $scss->compile('@import "pill.scss";'); ?>
  <?= $scss->compile('@import "activity_header.scss";'); ?>
</style>