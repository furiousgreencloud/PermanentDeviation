// CONSTANTS

var DEFAULT_CODE = 'void setup() {\n  size(400,400);\n}\n\nvoid draw(){\n  // anything you like\n}\n';
var RETRY_DELAY = 15;	// number of seconds after submit or timeout that one needs to
						// wait to try coding again

// GLOBALS

var state = {};

// UTILS
// =====

// First, checks if it isn't implemented yet.
if (!String.prototype.format) {
	String.prototype.format = function() {
		var args = arguments;
		return this.replace(/{(\d+)}/g, function(match, number) {
				return typeof args[number] != 'undefined'
				? args[number]
				: match
				;
				});
	};
}

function getQueryParams(qs) {
    qs = qs.split("+").join(" ");

    var params = {}, tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])]
            = decodeURIComponent(tokens[2]);
    }

    return params;
}

function appendArgs(base, args) {
	var phpArgs = new Array();
	for (a in args) {
		phpArgs.push('' + a + "=" + args[a]);
	}
	phpArgs = phpArgs.join('&');
	//console.log(phpArgs);
	return 'getReplayTimes.php' + ((phpArgs.length) ? ('?' + phpArgs) : '');
}

function pad(n, width, z) {
	z = z || '0';
	n = n + '';
	return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function durToHMS(d) {
	var h,m,s;
	h = Math.floor(d/(60*60));
	m = Math.floor((d-(h*60*60))/60);
	s = Math.floor(d%60);
	//return "{0}:{1}:{2}".format(pad(h,2),pad(m,2),pad(s,2));
	return "{0}h {1}m {2}s".format(pad(h,2),pad(m,2),pad(s,2));
}

function durToFriendlyString(d) {
	var h,m,s;
	d = Math.floor(d/(24*60*60));
	h = Math.floor(d/(60*60));
	m = Math.floor((d-(h*60*60))/60);
	s = Math.floor(d%60);
	var ret = "";
	if (d > 0) {
		if (d == 1) {
			return "yesterday";
		} else {
			return "" + d + " days ago"
		}
	}
	if (h>0) {
		if (h == 1) {
			return "about an hour ago";
		} else {
			return "a few hours ago"
		}
	}
	if (h>0) {
		if (h == 1) {
			return "an hour";
		} else {
			return "a few hours ago";
		}
	}
	if (m>0) {
		if (m == 1) {
			return "a minute ago";
		} else {
			return "a few minutes ago";
		}
	}
	if (s == 0) {
			return "now";
	} else if (s == 1) {
		return "a second ago";
	} else {
		return "" + s + "seconds ago";
	}
}


function isNavKey(keyCode) {
	return (keyCode == 38 || keyCode == 37 || keyCode == 39 || keyCode == 40
		||	keyCode == 91
		||  keyCode == 16 || keyCode == 17 || keyCode == 18 || keyCode == 20);
// shift	16
// ctrl		17
// alt	 18
// caps lock	 20
// page up	 33
// page down	 34
// end	 35
// home	 36
// left arrow	 37
// up arrow	 38
// right arrow	 39
// down arrow	 40
}

function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}

function isEmpty(str) {
    return (!str || 0 === str.length);
}

function now_sec() {
	 return Math.floor(new Date().getTime()/1000);
}

function time() {
	return now_sec();
}

function timeDiff_sec(start,end) { // if end is not provode uses now
	if (!end) {
	 end = now_sec()
	}
	return end - start
}

function calcRemainingTime(endTime) {
	var now = new Date().getTime()/1000;
	return endTime - now;
}


function getCaret(el) {
  if (el.selectionStart) {
    return el.selectionStart;
  } else if (document.selection) {
    el.focus();

    var r = document.selection.createRange();
    if (r == null) {
      return 0;
    }

    var re = el.createTextRange(),
        rc = re.duplicate();
    re.moveToBookmark(r.getBookmark());
    rc.setEndPoint('EndToStart', re);

    return rc.text.length;
  }
  return 0;
}

// Site Specific Functions

function href(page,params) {
	var url = "http://permanentdeviation.com/"+page+".php";
  // NOCOMMIT
  url = page + ".php";

	if (params) {
		var keyValuePairs = new Array();
		console.log(params);
		for(var key in params) {
			keyValuePairs.push(key+'='+params[key]);
		}
		url += '?' + keyValuePairs.join('&');
	}
	console.log("Prepared: " + url);
	return url;
}


// COMON FUNCTIONS
// ===============

function download(callback_fn,time_sec) // is time_sec is missiing latest is returned
{
	if (!time_sec) {
		time_sec = "latest";
	}

// DEBUG ALL RESPONSES
$.getJSON('download.php', { frame: time_sec } , function(data,textStatus,jqXHR) {
    //console.log("Normal Data: " + data);
    //console.log("Normal Status: " + textStatus);
    //console.log("Normal Response: " + jqXHR.responseText);
//As of 1.5 we have these methods:
}).always(
    function(jqXHR, textStatus, errorThrown) { //on failure
        //console.log("always/fail: " + jqXHR.responseText);
}).always(
    function(data, textStatus, jqXHR) { //on success
		//console.log("download response"); // DEBUG
		if (data) {
			console.log(data ); // DEBUG
			var codeEl = document.getElementById("code");
      // Need to update previousValue which is used by nopaste.js to detect pasting.
			codeEl.previousValue = codeEl.value =  unescape(data["code"]);
			if (callback_fn != undefined) {
				callback_fn(data);
			}
		}
}).fail(
    function(jqXHR, textStatus, errorThrown) {
        //console.log("fail: " + jqXHR.responseText);
//As of 1.8 we have this method also:
}).then(
/*
    function(data, textStatus, jqXHR) {
        console.log("then/sucess: " + jqXHR.responseText);},
    function(jqXHR, textStatus, errorThrown) {
        console.log("then/fail: " + jqXHR.responseText);
}
*/
);
/*

	$.getJSON('download.php', { frame: time_sec }, function(data) {
		console.log("download response"); // DEBUG
		if (data) {
			console.log(data ); // DEBUG
			document.getElementById("code").value =  unescape(data["code"]);
			if (callback_fn != undefined) {
				callback_fn(data);
			}
		}
		});
*/
}

function nameSet() {
	var nameEl = document.getElementById("name");
	if (!nameEl) {
		console.warn("Can't Find name Element");
		return;
	}
	console.log("Name Set to : " + nameEl.value.trim());
	state['name'] = nameEl.value.trim();
	saveStateToCookie();
	if (typeof upload == 'function') upload('update');
}

function startSketch() {
	switchSketchState(true);
}

function stopSketch() {
	switchSketchState(false);
}

function switchSketchState(on) {
	var processingInstance = Processing.getInstanceById('sketch');
	if (!processingInstance) return;
	if (on) {
		processingInstance.loop();  // call Processing loop() function
	} else {
		processingInstance.noLoop(); // stop animation, call noLoop()
	}
}

function saveStateToCookie() {
	console.log("STATE -> COOKIE:...");
	console.log(state);
	if (!state.score) {
		state.score = 0;
	}
	console.log(JSON.stringify(state));
	document.cookie = "pcii=" + encodeURIComponent(JSON.stringify(state));
	console.log(document.cookie);
}

var pciPattern = /pcii=[^\s]+[^$;\s]/;

function initCookieToState() {
	if (!document.cookie) return;
	console.log(document.cookie);
	console.log("Pattern : " + pciPattern);
	console.log("PCI Cookie: " + pciPattern.exec(document.cookie));
	state = eval(decodeURIComponent(pciPattern.exec(document.cookie)));
	console.log("COOKIE -> STATE ...");
	console.log(state);
}


function toggleFullScreen() {
  if (!document.fullscreenElement &&    // alternative standard method
      !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
      document.documentElement.msRequestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }
}

function createDownloadLink(cavasId,filename) {
	var a = document.createElement("a");
	var canvas = document.getElementById("sketch");
	var img    = canvas.toDataURL("image/png");
	a.download = filename;
	a.title = "Download Snapshot";
	a.innerHTML = '<img border="1" height="30" width="30" src="'+img+'" />';
	a.href = img;
	return a;
}
