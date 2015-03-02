<!DOCTYPE html>
<html>
  <head>
    <title>Upload0r</title>
    <link rel="stylesheet" type="text/css" href="assets/famous.css" />    
    <link rel="stylesheet" type="text/css" href="assets/app.css" />    
    <script type="text/javascript" src="assets/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="assets/famous-global.min.js"></script>
    <script type="text/javascript">
<? if ($_FILES): ?>
      var uploadAttempted = true;
<? else: ?>
      var uploadAttempted = false;
<? endif ?>
      <? if (isset($exceptions)): ?>var exceptions = <? echo $exceptions ?>;
<? endif ?>
      <? if (isset($uploaded_files)): ?>var uploadedFiles = <? echo $uploaded_files ?>;
<? endif ?>
    </script>
    <script type="text/javascript" src="assets/app.js"></script>
  </head>
  <body>
    <div class="noscript">
      Bitte schalte JavaScript ein, sonst geht hier gar nichts.
    </div>
    <script type="text/x-upload0r" id="header">
      <h1>upload0r</h2>
      <p>{{text}}</p>
    </script>
    <script type="text/x-upload0r" id="uploader">
      <form method="post" enctype="multipart/form-data">
        <input type="file" name="file[]" multiple>
        <input type="submit" value="H0chladen!">
      </form>
    </script>
    <script type="text/x-upload0r" id="footer">
      <p>Made with PHP, jQuery, famo.us and love &lt;3</p>
    </script>
    <script type="text/x-upload0r" id="file-card">
      <strong><a href="{{link}}" target="_blank">{{file}}</a></strong>
      <small>{{size}} KB {{isThumbnail}}</small>
      <div>
        {{preview}}
      </div>
    </script>
  </body>
</html>
