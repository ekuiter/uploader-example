<?

abstract class Hook {
  abstract public function execute($file, $uploader);

  public static function execute_all($hooks, $file, $uploader) {
    foreach ($hooks as $hook)
      $hook->execute($file, $uploader);
  }
}

class ResizeHook extends Hook {
  private $type, $dimension;

  function __construct($type, $dimension) {
    $this->type = $type;
    $this->dimension = $dimension;
  }

  public function execute($file, $uploader) {
    if ($file->type !== $this->type)
      return;
    $image = new Image($file);
    $width = $image->getWidth();
    $height = $image->getHeight();
    if ($width <= $this->dimension && $height <= $this->dimension)
      return;

    if ($width > $height)
      $image->resizeToWidth($this->dimension);
    if ($height >= $width)
      $image->resizeToHeight($this->dimension);

    $tempname = tempnam(sys_get_temp_dir(), "resized");
    $image->save($tempname, 0666);
    $thumbnail_file = new File(array(
      "tmp_name" => $tempname,
      "type" => "image/jpg",
      "size" => filesize($tempname)
    ));
    $thumbnail_file->original_name = "(Thumbnail) " . $file->original_name;
    $thumbnail_file->destination_name($file->destination_id() . ".thumb.jpg");
    $uploader->upload($thumbnail_file);
  }
}
