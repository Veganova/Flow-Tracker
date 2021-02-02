<meta name="viewport" content="width=device-width, initial-scale=1">
<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/@popperjs/core@2" charset="utf-8"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

<?php
  require_once $ROOT.'vendor/autoload.php';
  set_include_path(get_include_path() . PATH_SEPARATOR . $ROOT."vendor");

  require_once "scssphp/scssphp/scss.inc.php";
  use ScssPhp\ScssPhp\Compiler;
  $scss = new Compiler();
  $scss->setImportPaths($ROOT.'styles');
  
  function getStyleFiles($root_path) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root_path));

    $files = array(); 
    foreach ($rii as $file)
        if (!$file->isDir()) {
          $style_path = $file->getPathname();
          if (strpos($style_path, $root_path) === 0) $style_path = substr($style_path, strlen($root_path));
          
          $files[] = $style_path;
        }

    return $files;
  }
?>

<style>
  <?php
    $style_paths = getStyleFiles($ROOT."styles/");
    foreach($style_paths as $style_path) {
      echo $scss->compile('@import "'.$style_path.'";'); 
    }
  ?>
</style>