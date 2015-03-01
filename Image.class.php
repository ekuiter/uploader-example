<?

class Image {
  var $image, $type;

  function __construct($file) {
    $imagesize = getimagesize($file->name);
    $this->type = $imagesize[2];
    if ($this->type === IMAGETYPE_JPEG)
      $this->image = imagecreatefromjpeg($file->name);
    if ($this->type === IMAGETYPE_GIF)
      $this->image = imagecreatefromgif($file->name);
    if ($this->type === IMAGETYPE_PNG)
      $this->image = imagecreatefrompng($file->name);
  }

  public function save($filename, $permissions = 0600, $compression = 90) {
    imagejpeg($this->image, $filename, $compression);
    chmod($filename, $permissions);
  }
  
  public function resizeToHeight($height) {
    $ratio = $height / $this->getHeight();
    $width = $this->getWidth() * $ratio;
    $this->resize($width, $height);
  }

  public function resizeToWidth($width) {
    $ratio = $width / $this->getWidth();
    $height = $this->getheight() * $ratio;
    $this->resize($width, $height);
  }

  public function resize($width, $height) {
    $new_image = imagecreatetruecolor($width, $height);
    imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
    $this->image = $new_image;
  }

  public function getWidth() {
    return imagesx($this->image);
  }

  public function getHeight() {
    return imagesy($this->image);
  }
}

