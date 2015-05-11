(function(global) {

  var canvas = document.getElementById('sketch'),
    code = document.getElementById('code'),
    output = document.getElementById('output'),
    instance = null;

  function createCanvas() {
    // Make a new canvas, in case we're switching from 2D to 3D contexts.
    var container = document.getElementById('sketch-container');
    var sketch = document.getElementById('sketch');
    container.removeChild(sketch);

    sketch = document.createElement('canvas');
    sketch.id = 'sketch';
    container.appendChild(sketch);

    return sketch;
  }

  function waitForExit() {
    var checkbox = document.getElementById('expect-exit-callback');
    if (!checkbox) {
      return false;
    }
    return checkbox.checked || checkbox.value;
  }

  global.runSketch = function(callbackError,callbackSuccess,callbackTerm) {
	  console.log("runSketch()");
	  try {
		  if (instance) {
			  console.log("stopped old instance");
			  instance.noLoop();
			  instance = null;
		  }
		  output.value = '';
	      console.log("console cleared");

		  canvas = createCanvas();
	      //console.log("canvas created");
		  var sketch = Processing.compile(code.value);
	      //console.log("sketch created from.. ");
	      //console.log(code.value);
		  if (callbackTerm) {
			  if (!/exit\(\);/.test(code.value)) {
				  throw "exit() not found in sketch. Add the exit() command, and re-run the sketch.";
			  }
			  sketch.onExit = callbackTerm;
			  instance = new Processing(canvas, sketch);
	      	  console.log("instance created");
		  } else {
			  instance = new Processing(canvas, sketch);
	      	  console.log("instance created");
		  }
		  window.status = "Playing Sketch";
		  if (callbackSuccess) callbackSuccess();
	  } catch (e) {
		  output.value = "Error! Error was:\n" + e.toString();
		  console.log(e.toString());
		  console.log(output);
		  if (callbackError) callbackError(e);
	  }
  };

  global.convertToJS = function() {
    try {
      output.value = js_beautify(
        Processing.compile(code.value).sourceCode).replace(/\n\n\n+/g, '\n\n');
      output.select();
    } catch (e) {
      output.value = "Parser Error! Error was:\n" + e.toString();
    }
  };

  global.generateDataURI = function() {
    // Run the sketch first, in case the user hasn't
    runSketch();
    output.value = canvas.toDataURL();
    output.select();
  };

  function buildRefTest() {
    try {
      // if the test was 2d, we can just call getImageData
      if (!instance.use3DContext) {
        var context = canvas.getContext('2d');
        var imgData = context.getImageData(0, 0, canvas.width, canvas.height).data;
      // else, we'll need to call WebGL's readPixels.
      } else {
        // The order of the pixels go from bottom to top, left to right.
        var context = canvas.getContext("experimental-webgl");
        var imgData = new Uint8Array(canvas.width * canvas.height * 4);
          context.readPixels(0, 0, canvas.width, canvas.height, context.RGBA, context.UNSIGNED_BYTE, imgData);
        }

        var pixels = [];
        for(var i = 0, idl = imgData.length; i < idl; i++) {
          pixels[i] = imgData[i];
        }

        var dimensions = "[" + canvas.width + "," + canvas.height + "]";
        // Opera doesn't have btoa() so this won't work there.
        document.location.href= "data:text/plain;charset=utf-8;base64," +
          btoa('//' + dimensions + pixels + '\n' + code.value);
    } catch (e) {
        output.value = "Error creating ref test! Error was: " + e.toString();
    }
  };

  global.generateRefTest = function() {
    // Run the sketch first, in case the user hasn't
    runSketch(buildRefTest);
  };

}(window));
