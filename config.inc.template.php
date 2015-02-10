<?

$config = array(
  "upload_mode" => "ftp", // entweder "ftp" oder "local"
  "ftp" => array(
    "server" => "server.de",
    "user" => "username",
    "pass" => "password",
    // hier werden die Uploads abgelegt
    "uploaddir" => "/var/www/upload0r/uploads", // absoluter Pfad
  ),
  "local" => array(
    "uploaddir" => "uploads" // zu upload.php relativer Pfad
  ),
  "httpdir" => "/upload0r/uploads", // hier sind die Uploads öffentlich zugänglich
  "permissions" => array(
    // Reihenfolge irrelevant
    "types" => array(
      //new AllowAllPermission(), // alle Dateitypen erlauben
      new TypePermission("image/png", "png"), // speziellen Dateityp erlauben
      new TypePermission("image/jpg", "jpg"), // "MIME-Typ", "Dateiendung"
      new TypePermission("image/jpeg", "jpg"),
      new TypePermission("image/gif", "gif"),
      new TypePermission("application/pdf", "pdf")
      // alle nicht aufgeführten Typen werden automatisch verboten
    ),
    // Reihenfolge wird ausgewertet, man kann bspw. eine AllowAllPermission
    // ganz ans Ende setzen, um für bestimmte Dateitypen Maximalgrößen festzulegen
    // und für andere offen zu lassen
    "sizes" => array( 
      new SizePermission("image/png", 2048), // spezielle Größe für Dateityp
      new SizePermission("image/jpg", 2048), // "MIME-Typ", "Dateigröße in KB"
      new SizePermission("image/jpeg", 2048),
      new SizePermission("image/gif", 2048),
      new SizePermission("application/pdf", 4096)
      // new AllowAllPermission(), // alle Dateigrößen erlauben
      // new SizePermission(null, 2048), // gleiche Maximalgröße für alle Dateitypen 
    ),
    // auch hier wird die Reihenfolge ausgewertet
    "is_image" => array(
      new IsImagePermission("image/png"), // Bilddateien werden auf Echtheit
      new IsImagePermission("image/jpg"), // überprüft, damit kein Code
      new IsImagePermission("image/jpeg"), // eingeschleust wird
      new IsImagePermission("image/gif"), 
      new AllowAllPermission() // PDF-Dateien werden nicht speziell geprüft
    )
  )
);
