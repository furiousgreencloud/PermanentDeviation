
		function matchCount(regex,str) {
			var m =  str.match(regex);
			if (m == null) return 0;
			return m.length; 
		}

		function countNumbers(str) {
			return matchCount(
				/-{0,1}\d+\.{0,1}[\d]*/g
				,str);
		}

		function countComments(str) {
			return matchCount(
				/\/\/.*$|\/\*/gm
				,str);
		}

		function countReservedWords(str) {
			return matchCount(
/true|false|break|case|catch|continue|debugger|default|delete|do|else|finally|for|function|if|in|instanceof|new|return|switch|this|throw|try|typeof|var|void|while|with/g
				,str);
		}

		function countProcessingSpecial(str) {
			return matchCount( 
/draw\(|exit\(|loop\(|noLoop\(|popStyle\(|pushStyle\(|redraw\(|setup\(|cursor\(|displayHeight|displayWidth|focused|frameCount|frameRate\(|frameRate|height|noCursor\(|size\(|width|Array|ArrayList|FloatDict|FloatList|HashMap|IntDict|IntList|JSONArray|JSONObject|Object|String|StringDict|StringList|Table|TableRow|binary\(|boolean\(|byte\(|char\(|float|hex\(|int\(|str\(|unbinary\(|unhex\(|join\(|match\(|matchAll\(|nf\(|nfc\(|nfp\(|nfs|split\(|splitTokens\(|trim\(|append\(|arrayCopy\(|concat\(|expand\(|reverse\(|shorten\(|sort\(|splice\(|subset\(|!=|<|<=|==|>|>=|!|&&|\|\||createShape\(|loadShape\(|PShape|arc\(|ellipse\(|line\(|point\(|quad\(|rect\(|triangle\(|bezier\(|bezierDetail\(|bezierPoint\(|bezierTangent\(|curve\(|curveDetail\(|curvePoint\(|curveTangent\(|curveTightness\(|box\(|sphere\(|sphereDetail\(|ellipseMode\(|noSmooth\(|rectMode\(|smooth\(|strokeCap\(|strokeJoin\(|strokeWeight\(|beginContour\(|beginShape\(|bezierVertex\(|curveVertex\(|endContour\(|endShape\(|quadraticVertex\(|vertex\(|shape\(|shapeMode\(|mouseButton|mouseClicked\(|mouseDragged\(|mouseMoved\(|mousePressed\(|mousePressed|mouseReleased\(|mouseWheel\(|mouseX|mouseY|pmouseX|pmouseY|key|keyCode|keyPressed\(|keyPressed|keyReleased\(|keyTyped\(|BufferedReader|createInput\(|createReader\(|loadBytes\(|loadJSONArray\(|loadJSONObject\(|loadStrings\(|loadTable\(|loadXML\(|open\(|parseXML\(|selectFolder\(|selectInput\(|day\(|hour\(|millis\(|minute\(|month\(|second\(|year\(|print\(|printArray\(|println\(|save\(|saveFrame\(|beginRaw\(|beginRecord\(|createOutput|createWriter\(|endRaw\(|endRecord\(|PrintWriter|saveBytes\(|saveJSONArray\(|saveJSONObject\(|saveStream\(|saveStrings\(|saveTable\(|saveXML\(|selectOutput\(|applyMatrix\(|popMatrix\(|printMatrix|pushMatrix\(|resetMatrix\(|rotate\(|rotateX\(|rotateY\(|rotateZ\(|scale\(|shearX\(|shearY\(|translate\(|ambientLight\(|directionalLight\(|lightFalloff\(|lights\(|lightSpecular\(|noLights\(|normal\(|pointLight\(|spotLight\(|beginCamera\(|camera\(|endCamera\(|frustum\(|ortho\(|perspective\(|printCamera\(|printProjection\(|modelX\(|modelY\(|modelZ\(|screenX\(|screenY\(|screenZ\(|ambient\(|emissive\(|shininess\(|specular\(|background\(|clear\(|colorMode\(|fill\(|noFill\(|noStroke\(|stroke\(|alpha\(|blue\(|brightness\(|color\(|green\(|hue\(|lerpColor\(|red\(|saturation\(|createImage\(|PImage|image\(|imageMode\(|loadImage\(|noTint\(|requestImage\(|tint\(|texture\(|textureMode\(|textureWrap\(|blend\(|copy\(|filter\(|get\(|loadPixels\(|pixels\[|set\(|updatePixels\(|blendMode\(|createGraphics\(|PGraphics|loadShader\(|PShader|resetShader\(|shader\(|PFont|createFont\(|loadFont\(|text\(|textFont\(|textAlign\(|textLeading\(|textMode\(|textSize\(|textWidth\(|textAscent\(|textDescent\(|PVector|%|\*|\*=|\+|\+\+|\+=|-|--|-=|/|/=|&|<<|>>|\||abs\(|ceil\(|constrain\(|dist\(|exp\(|floor\(|lerp\(|log\(|mag\(|map\(|max\(|min\(|norm\(|pow\(|round\(|sq\(|sqrt\(|acos\(|asin\(|atan\(|atan2\(|cos\(|degrees\(|radians\(|sin\(|tan\(|noise\(|noiseDetail\(|noiseSeed\(|random\(|randomGaussian\(|randomSeed\(|HALF_PI|PI|QUARTER_PI|TAU|TWO_PI/g
				,str);
		}

