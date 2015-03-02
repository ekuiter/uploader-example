$(function() {
  var Engine = famous.core.Engine;
  var Surface = famous.core.Surface;
  var Modifier = famous.core.Modifier;
  var Transform = famous.core.Transform;
  var Easing = famous.transitions.Easing;

  function surfaceWithTemplate(template, options, data) {
    if (!data) data = { };
    var content = $("#" + template).html();
    $.each(data, function(key, value) {
      content = content.replace(new RegExp("{{" + key + "}}", "g"), value);
    });
    return new Surface($.extend({
      content: content,
      classes: [template]
    }, options));
  }

  function scaleModifier(factor) {
    return new Modifier({
      transform: Transform.scale(factor, factor)
    });
  }

  function translateModifier(x, y) {
    return new Modifier({
      transform: Transform.translate(x, y)
    });
  }

  function scaleAnimationModifier(surface) {
    var modifier = new Modifier();
    
    function addScaleAnimationToModifier(factor) {
      return function() {
        modifier.setTransform(
          Transform.scale(factor, factor),
          { duration: 100, curve: "easeInOut" }
        );
      };
    }

    surface.on("mouseover", addScaleAnimationToModifier(1.1));
    surface.on("mouseout", addScaleAnimationToModifier(1));
    return modifier;
  }

  function rollInAnimationModifier(surface, moveX, moveY) {
    var modifier = new Modifier();

    window.setTimeout(function() {
      modifier.setTransform(
        Transform.translate(moveX, moveY),
        { duration: 1000, curve: Easing.inOutBack }
      );
    }, 100);

    return modifier;
  }

  function addHoverableSurface(node, surface, animationModifierFunc, callback) {
    var hoverSurface = new Surface({ size: surface.size });
    node.add(animationModifierFunc(hoverSurface)).add(surface);
    node.add(hoverSurface);
    if (callback) callback(hoverSurface);
  }

  var centerMod = new Modifier({
      origin: [0.5, 0.5],
      align: [0.5, 0.50]
  });

  var background = new Surface({ classes: ["background"] });  
  var mainContext = Engine.createContext();
  var windowWidth = mainContext.getSize()[0];
  var windowHeight = mainContext.getSize()[1];
  mainContext.add(background);
  var node = mainContext.add(centerMod);

  if (uploadAttempted)
    makeUploadPage();
  else 
    makeHomePage();  

  function makeHeader(text) {
    addHoverableSurface(
      node
        .add(translateModifier(0, -windowHeight/2 - 150))
        .add(rollInAnimationModifier(header, 0, windowHeight/2)),
      surfaceWithTemplate("header", { size: [250, 130] }, { text: text }),
      scaleAnimationModifier, function(hoverSurface) {
        hoverSurface.properties = { cursor: "pointer" };
        hoverSurface.on("click", function() {
          window.location.href = "upload.php";
        });
      }
    );
  }

  function makeHomePage() {
    makeHeader("Proudly presented by Leon, Tim, Tizian, Josef and Elias");
    var uploader = surfaceWithTemplate("uploader", { size: [350, 46] }),
        submitAnimationModifier = new Modifier();
    node
      .add(scaleModifier(1.6))
      .add(translateModifier(-windowWidth/2, 0))
      .add(rollInAnimationModifier(uploader, windowWidth/2, 0))
      .add(submitAnimationModifier)
      .add(uploader); 
    addHoverableSurface(
      node
        .add(translateModifier(0, windowHeight/2 + 100))
        .add(rollInAnimationModifier(header, 0, -windowHeight/2)),
      surfaceWithTemplate("footer", { size: [330, 40] }),
      scaleAnimationModifier, function(hoverSurface) {
        hoverSurface.properties = { cursor: "pointer" };
        hoverSurface.on("click", function() {
          window.open("http://github.com/ekuiter/uploader-example", "_blank");
        });
      }
    );

    Engine.on("submit", function(e) {
      e.preventDefault();
      var errors = [], imagesToCheck = 0;

      if (config.enableChecks) {
        if (!$(".uploader form input").val())
          errors.push({ file: null, message: "Es wurde keine Datei ausgew&auml;hlt." });

        var files = $(".uploader form input").get(0).files;
        $.each(files, function(_, file) {
          if ($.inArray(file.type, config.types) === -1)
            errors.push({ file: file.name, message: "Dateityp nicht erlaubt." });

          var configObject = getConfigObject(config.sizes, file);
          if (configObject) {
            var maxSize = configObject.size;
            if (file.size / 1024 > maxSize)
              errors.push({ file: file.name, message: "Datei gr&ouml;&szlig;er als " + maxSize + " KB." });
          }

          if (file.type.match(/^image\//))
            imagesToCheck++;
        });

        if (imagesToCheck === 0)
          proceed();
        else
          $.each(files, function(_, file) {
            if (file.type.match(/^image\//)) {
              var reader = new FileReader();
              var image  = new Image();
              reader.readAsDataURL(file);  
              reader.onload = function(_file) {
                  image.src = _file.target.result;
                  image.onload = function() {
                    checkDimensions(file, this.width, this.height);
                  };
              };
            }
          });
      } else
        proceed();
      
      function getConfigObject(configArray, file) {
        var obj;
        $.each(configArray, function(_, configObj) {
          if (configObj.type == file.type)
            obj = configObj;
        });
        return obj;
      }

      function checkDimensions(file, width, height) {
        var maxDimensions = getConfigObject(config.dimensions, file);
        if (maxDimensions) {
          if (width > maxDimensions.width && height > maxDimensions.height)
            errors.push({ file: file.name, message: "Das Bild ist zu breit und zu hoch (> " + maxDimensions.width + "px)." });
          else if (width > maxDimensions.width)
            errors.push({ file: file.name, message: "Das Bild ist zu breit (> " + maxDimensions.width + "px)." });
          else if (height > maxDimensions.height)
            errors.push({ file: file.name, message: "Das Bild ist zu hoch (> " + maxDimensions.height + "px)." });
        }
        imagesToCheck--;
        if (imagesToCheck === 0)
          proceed();
      }

      function proceed() {
        if (errors.length > 0) {
          makeErrors(errors);
          return;
        }

        (function pulseAnimation() {
          submitAnimationModifier.setTransform(
            Transform.scale(0.9, 0.9),
            { duration: 500, curve: Easing.inOutBack }, function() {
              submitAnimationModifier.setTransform(
                Transform.scale(1, 1),
                { duration: 500, curve: "easeInOut" }, pulseAnimation
              );
            }
          );
        })();
        $(".uploader form").submit();
      }
    });
  }

  function makeUploadPage() {
    makeErrors(exceptions);
    var defaultCardSize = 250, cardMargin = 20, fileCount = uploadedFiles.length,
        cardSize = (windowWidth * 0.9 - fileCount * cardMargin) / fileCount;
    if (cardSize > defaultCardSize) cardSize = defaultCardSize;
    var cardSizeWithMargin = cardSize + cardMargin,
        cardsWidth = fileCount * cardSizeWithMargin;
    if (!fileCount)
      makeHeader("Es konnte keine Datei hochgeladen werden.");
    else
      makeHeader("Folgende Dateien wurden erfolgreich hochgeladen:");
    node = node.add(translateModifier(-cardsWidth / 2 + cardSizeWithMargin / 2, 0));
    $.each(uploadedFiles, function(i, uploadedFile) {
      var file = uploadedFile.file, isThumbnail = "(verkleinert)";
      if (file.match(/^\(Thumbnail\) /))
        file = uploadedFile.file.substr(12);
      else
        isThumbnail = "";
      if (uploadedFile.type === "application/pdf")
        var preview = "<em>(PDF-Dokument)</em>";
      else if (uploadedFile.type.match(/^image\//))
        var preview = "<img src=\"" + uploadedFile.link + "\" alt=\"" + file + "\" style=\"max-width: " +
          (cardSize - 10) + "px; max-height: " + (cardSize - 60) + "px;\" />";
      else
        var preview = "<em>(Keine Vorschau verf&uuml;gbar)</em>";

      var fileCard = surfaceWithTemplate("file-card", { size: [cardSize, cardSize] }, {
        file: file,
        size: Math.round(uploadedFile.size / 1024),
        isThumbnail: isThumbnail,
        link: uploadedFile.link,
        preview: preview
      });

      var fileCardNode = node
        .add(translateModifier(i * cardSizeWithMargin, 50))
        .add(translateModifier(-windowWidth/2 - cardsWidth, 0))
        .add(rollInAnimationModifier(fileCard, windowWidth/2 + cardsWidth, 0));
      
      addHoverableSurface(fileCardNode, fileCard, scaleAnimationModifier, function(hoverSurface) {
        hoverSurface.properties = { cursor: "pointer" };
        hoverSurface.on("click", function() {
          window.open(uploadedFile.link, "_blank");
        });
      }); 
    });
  }

  function makeErrors(errors) {
    var errorWidth = 400, errorHeight = 60, margin = 10, errorWidthWithMargin = errorWidth + margin;
    $.each(errors, function(i, error) {
      var error = surfaceWithTemplate("error", { size: [errorWidth, errorHeight] },
        { file: error.file ? error.file : "upload0r", message: error.message });
      mainContext
      .add(new Modifier({ origin: [1, 0], align: [1, 0], transform: Transform.translate(0, 0, 1) }))
      .add(translateModifier(-margin, i * (errorHeight + margin) + margin))
      .add(translateModifier(errorWidthWithMargin, 0))
      .add(rollInAnimationModifier(error, -errorWidthWithMargin, 0))
      .add(error);
    });
  }

});
