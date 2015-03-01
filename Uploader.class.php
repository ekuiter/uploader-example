<?

abstract class Uploader {
  protected $uploaddir, $httpdir;
  public $uploaded_files = array();

  abstract protected function upload_internal($file, $filename);

  function __construct($uploaddir, $httpdir) {
    $this->uploaddir = $uploaddir;
    $this->httpdir = $httpdir;
  }

  public function upload($file) {
    $this->upload_internal($file, $file->destination_name());
    $this->uploaded_files[] = $file;
  }

  public function upload_path($file) {
    return "$this->httpdir/" . $file->destination_name();
  }
  
  public static function choose($config) {
    $upload_mode = $config["upload_mode"];
    $uploader_config = $config[$upload_mode];
    if ($upload_mode === "ftp")
      $uploader = new FtpUploader($uploader_config["server"], $uploader_config["user"],
                                  $uploader_config["pass"], $uploader_config["uploaddir"],
                                  $config["httpdir"]);
    else if ($upload_mode === "local")
      $uploader = new LocalUploader($uploader_config["uploaddir"], $config["httpdir"]);
    return $uploader;
  }
}

class FtpUploader extends Uploader {
  private $ftp;

  function __construct($server, $user, $pass, $uploaddir, $httpdir) {
    parent::__construct($uploaddir, $httpdir);
    $this->ftp = ftp_connect($server);
    $login = ftp_login($this->ftp, $user, $pass);

    if (!$this->ftp || !$login)
      throw new Exception("Verbindung fehlgeschlagen. Benutzername und Passwort überprüfen.");

    ftp_pasv($this->ftp, true);
  }

  function __destruct() {
    ftp_close($this->ftp);
  }

  protected function upload_internal($file, $filename) {
    $upload = ftp_put($this->ftp, "$this->uploaddir/$filename", $file->name, FTP_BINARY);
    if (!$upload)
      throw new Exception("Upload fehlgeschlagen.");
  }
}

class LocalUploader extends Uploader {
  protected function upload_internal($file, $filename) {
    $new_name = "$this->uploaddir/$filename";
    if (!move_uploaded_file($file->name, $new_name))
      rename($file->name, $new_name);
  }
}
