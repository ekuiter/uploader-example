<?

abstract class Permission {
  abstract public function permit($file);

  public static function permit_some($permissions, $file) {
    $result = array("permitted" => false);
    foreach ($permissions as $permission) {
      $result = $permission->permit($file);
        if ($result["permitted"])
          break;
    }
    if (!$result["permitted"])
      throw new PermissionException($result["payload"]);
    return $result["payload"];
  }
}

class AllowAllPermission extends Permission {
  public function permit($file) {
    return array("permitted" => true);
  }
}

class TypePermission extends Permission {
  private $type, $type_extension;
  
  function __construct($type, $type_extension) {
    $this->type = $type;
    $this->type_extension = $type_extension;
  }

  public function permit($file) {
    return array(
      "permitted" => $file->type === $this->type,
      "payload" => $this->type_extension
    );
  }
}

class SizePermission extends Permission {
  private $type, $size;

  function __construct($type, $size) {
    $this->type = $type;
    $this->size = $size * 1024;
  }

  public function permit($file) {
    if (!$this->type)
      return array("permitted" => $file->size < $this->size, "payload" => $this->size);
    else if ($file->type === $this->type)
      if ($file->size < $this->size)
        return array("permitted" => true, "payload" => $this->size);
      else
        throw new PermissionException($this->size);
    else
      return array("permitted" => false, "payload" => $this->size);
  }  
}

class IsImagePermission extends Permission {
  private $type;

  function __construct($type) {
    $this->type = $type;
  }

  public function permit($file) {
    if ($file->type === $this->type)
      if (exif_imagetype($file->name))
        return array("permitted" => true);
      else
        throw new PermissionException(null);
    else
      return array("permitted" => false);
  }
}

class DimensionsPermission extends Permission {
  private $type, $width, $height;

  function __construct($type, $width, $height) {
    $this->type = $type;
    $this->width = $width;
    $this->height = $height;
  }

  public function permit($file) {
    if ($file->type === $this->type) {
      $imagesize = getimagesize($file->name);
      if ($imagesize[0] > $this->width && $imagesize[1] > $this->height)
        throw new PermissionException("zu breit und zu hoch (> {$this->width}x{$this->height}px)");
      else if ($imagesize[0] > $this->width)
        throw new PermissionException("zu breit (> {$this->width}px)");
      else if ($imagesize[1] > $this->height)
        throw new PermissionException("zu hoch (> {$this->height}px)");
      return array("permitted" => true);
    } else
      return array("permitted" => false);
  }
}

class PermissionException extends Exception {
  public $payload;

  function __construct($payload) {
    $this->payload = $payload;
  }
}
