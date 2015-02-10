<?

class File {
  public $name, $type, $size;
  private $type_extension, $destination_name;
  
  public static function sanitize_files($files) {
    $file_count = count($files["name"]);
    $new_files = array();
    for ($i = 0; $i < $file_count; $i++)
      $new_files[] = array(
        "name" => $files["name"][$i],
        "type" => $files["type"][$i],
        "tmp_name" => $files["tmp_name"][$i],
        "error" => $files["error"][$i],
        "size" => $files["size"][$i]
      );
    return $new_files;
  }

  function __construct($uploaded_file, $permissions) {
    if (!$uploaded_file)
      throw new Exception("Keine Datei hochgeladen.");

    $this->name = $uploaded_file["tmp_name"];
    $this->type = $uploaded_file["type"];
    $this->size = $uploaded_file["size"];

    try {
      $this->type_extension = Permission::permit_some($permissions["types"], $this); 
    } catch(PermissionException $e) {
      throw new Exception("Dateityp nicht erlaubt.");
    }

    try {
      Permission::permit_some($permissions["sizes"], $this);
    } catch (PermissionException $e) {
      throw new Exception("Datei größer als " . $e->payload / 1024 . " KB.");
    }

    try {
      Permission::permit_some($permissions["is_image"], $this);
    } catch (PermissionException $e) {
      throw new Exception("Das ist keine Bilddatei.");
    }
  }

  public function destination_name() {
    if (!$this->destination_name)
      $this->destination_name = uniqid(rand(), true) . "." . $this->type_extension;
    return $this->destination_name;
  }
}
