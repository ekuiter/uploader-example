<?

require_once "Uploader.class.php";
require_once "File.class.php";
require_once "Permission.class.php";
require_once "config.inc.php";

$files = File::sanitize_files($_FILES["file"]);
$uploader = Uploader::choose($config);
for ($i = 0; $i < count($files); $i++) {
  echo "<strong>Datei #$i:</strong> ";
  try {
    $file = new File($files[$i], $config["permissions"]);
    $link = $uploader->upload($file);
    echo <<<html
    <a href="$link">$link</a><br /><br />
    <iframe src="$link" width="800" height="600"></iframe>
html;
  } catch (Exception $e) {
    echo $e->getMessage() . "<br /><hr />";
  }
}
