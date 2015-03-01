<?

require_once "Uploader.class.php";
require_once "File.class.php";
require_once "Image.class.php";
require_once "Permission.class.php";
require_once "Hook.class.php";
require_once "config.inc.php";

$files = File::sanitize_files($_FILES["file"]);

$uploader = Uploader::choose($config);

for ($i = 0; $i < count($files); $i++) {
  try {
    $file = new File($files[$i], $config["permissions"]);
    $file->execute_hooks($config["hooks"], $uploader);
    $uploader->upload($file);
  } catch (Exception $e) {
    echo $e->getMessage() . "<br /><hr />";
  }
}

foreach ($uploader->uploaded_files as $uploaded_file) {
  $link = $uploader->upload_path($uploaded_file);
  echo <<<html
  <br /><a href="$link">$link</a><br /><br />
  <iframe src="$link" width="800" height="600"></iframe>
html;
}
