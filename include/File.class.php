<?

class File {
  public $name, $type, $size, $original_name;
  private $type_extension, $destination_id, $destination_override;
  
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

  function __construct($uploaded_file, $permissions = array()) {
    if (!$uploaded_file || !$uploaded_file["tmp_name"])
      throw new Exception("Es wurde keine Datei ausgewählt.");

    $this->name = $uploaded_file["tmp_name"];
    $this->type = $uploaded_file["type"];
    $this->size = $uploaded_file["size"];
    $this->original_name = $uploaded_file["name"];

    if ($permissions !== array()) {
      try {
        $this->type_extension = Permission::permit_some($permissions["types"], $this); 
      } catch(PermissionException $e) {
        throw new FileException($this, "Dateityp nicht erlaubt.");
      }

      try {
        Permission::permit_some($permissions["sizes"], $this);
      } catch (PermissionException $e) {
        throw new FileException($this, "Datei größer als " . $e->payload / 1024 . " KB.");
      }

      try {
        Permission::permit_some($permissions["is_image"], $this);
      } catch (PermissionException $e) {
        throw new FileException($this, "Das ist keine Bilddatei.");
      }
      
      try {
        Permission::permit_some($permissions["dimensions"], $this);
      } catch (PermissionException $e) {
        throw new FileException($this, "Das Bild ist {$e->payload}.");
      }
    }
  }

  public function execute_hooks($hooks, $uploader) {
    Hook::execute_all($hooks, $this, $uploader);    
  }

  public function destination_id() {
    if (!$this->destination_id)
      $this->destination_id = uniqid(rand(), true);
    return $this->destination_id;
  }

  public function destination_name($new_name = null) {
    if ($new_name)
      $this->destination_override = $new_name;
    if ($this->destination_override)
      return $this->destination_override;
    else
      return $this->destination_id() . "." . $this->type_extension;
  }
}

class FileException extends Exception {
  public $file;

  function __construct($file, $message) {
    parent::__construct($message);
    $this->file = $file;
  }
}
