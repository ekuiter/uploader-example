$(function() {
  var Engine = famous.core.Engine;
  var Surface = famous.core.Surface;
  var Modifier = famous.core.Modifier;
  var Transform = famous.core.Transform;
  var Easing = famous.transitions.Easing;

  function surfaceWithTemplate(template, options) {
    return new Surface($.extend({
      content: $("#" + template).html(),
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

  var background = new Surface({
    size: [undefined, undefined],
    classes: ["background"]
  });

  var centerMod = new Modifier({
      origin: [0.5, 0.5],
      align: [0.5, 0.50]
  });

  var header = surfaceWithTemplate("header", { size: [250, 130] });
  var uploader = surfaceWithTemplate("uploader", { size: [330, 46] });
  var footer = surfaceWithTemplate("footer", { size: [330, 40] });

  var mainContext = Engine.createContext();
  var windowWidth = mainContext.getSize()[0];
  var windowHeight = mainContext.getSize()[1];
  mainContext.add(background);
  var node = mainContext.add(centerMod);
  node
    .add(translateModifier(0, -windowHeight/2 - 150))
    .add(rollInAnimationModifier(header, 0, windowHeight/2))
    .add(scaleAnimationModifier(header))
    .add(header);
  node
    .add(scaleModifier(1.6))
    .add(translateModifier(-windowWidth/2, 0))
    .add(rollInAnimationModifier(uploader, windowWidth/2, 0))
    .add(uploader);
  node
    .add(translateModifier(0, windowHeight/2 + 100))
    .add(rollInAnimationModifier(header, 0, -windowHeight/2))
    .add(scaleAnimationModifier(footer))
    .add(footer);
});
