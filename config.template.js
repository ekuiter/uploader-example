var config = {
  // Die unten aufgeführten client-seitigen Sicherheits-Checks
  // können hier zu Demo-Zwecken ausgeschaltet werden.
  // Durch Einschalten entlastet man die Internetverbindung.
  // Durch Ausschalten werden zumindest die Dateien hochgeladen,
  // die die Sicherheitsprüfung erfolgreich durchlaufen.
  enableChecks: true,
  // Diese Optionen sind vergleichbar mit den entsprechenden
  // Optionen in config.php. Sie sind weniger mächtig und
  // KEINE echte Sicherheitsprüfung - diese findet erst auf
  // dem Server statt - sie dienen hier nur zum Einsparen
  // von Bandbreite.
  types: ["image/png", "image/jpg", "image/jpeg", "image/gif", "application/pdf"],
  sizes: [
    { type: "image/png", size: 2048 },
    { type: "image/jpg", size: 2048 },
    { type: "image/jpeg", size: 2048 },
    { type: "image/gif", size: 2048 },
    { type: "application/pdf", size: 4096 }
  ],
  dimensions: [
    { type: "image/jpg", width: 3000, height: 2000 },
    { type: "image/jpeg", width: 3000, height: 2000 }
  ]
};
