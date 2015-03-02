<?

$files = File::sanitize_files($_FILES["file"]);
$exceptions = array();
$uploaded_files = array();

try {
  $uploader = Uploader::choose($config);

  for ($i = 0; $i < count($files); $i++) {
    try {
      $file = new File($files[$i], $config["permissions"]);
      $file->execute_hooks($config["hooks"], $uploader);
      $uploader->upload($file);
    } catch (FileException $e) {
      $exceptions[] = array("file" => $e->file->original_name, "message" => $e->getMessage());
    } catch (Exception $e) {
      $exceptions[] = array("file" => null, "message" => $e->getMessage());
    }
  }
} catch (Exception $e) {
    $exceptions[] = array("file" => null, "message" => $e->getMessage());
}

$exceptions = json_encode($exceptions);
foreach ($uploader->uploaded_files as $file)
  $uploaded_files[] = array("file" => $file->original_name, "size" => $file->size,
    "link" => $uploader->upload_path($file), "type" => $file->type);
$uploaded_files = json_encode($uploaded_files);
