<?

class Uploader {
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

class FtpUploader {
  private $uploaddir, $httpdir, $ftp;

  function __construct($server, $user, $pass, $uploaddir, $httpdir) {
    $this->uploaddir = $uploaddir;
    $this->httpdir = $httpdir;
    $this->ftp = ftp_connect($server);
    $login = ftp_login($this->ftp, $user, $pass);

    if (!$this->ftp || !$login)
      throw new Exception("Verbindung fehlgeschlagen. Benutzername und Passwort überprüfen.");
  }

  function __destruct() {
    ftp_close($this->ftp);
  }

  public function upload($file) {
    $upload = ftp_put($this->ftp, "$this->uploaddir/" . $file->destination_name(), $file->name, FTP_BINARY);

    if (!$upload)
      throw new Exception("Upload fehlgeschlagen.");

    return "$this->httpdir/" . $file->destination_name();
  }
}

class LocalUploader {
  private $uploaddir, $httpdir;

  function __construct($uploaddir, $httpdir) {
    $this->uploaddir = $uploaddir;
    $this->httpdir = $httpdir;
  }

  public function upload($file) {
    move_uploaded_file($file->name, "$this->uploaddir/" . $file->destination_name());
    return "$this->httpdir/" . $file->destination_name();
  }
}
