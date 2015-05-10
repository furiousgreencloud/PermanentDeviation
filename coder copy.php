<?php
	require 'constants.php';
	//unset($_COOKIE['pcii']); // Used for DEBUGGING, somtimes


	// TODO use fine read/write access as lock on giving out keys
	// database
	function areFinal() {
		$serverState = file_get_contents("sandbox/state.json");
		if (!isset($serverState)) {
			return false;
		}
		$serverState = json_decode($serverState, true); // assoc: true
		return (isset($serverState) && $serverState['step'] == "final");
	}

	$cookie = array(); 
	if (isset($_COOKIE['pcii'])) {
		$cookie = urldecode($_COOKIE['pcii']);
		$cookie = json_decode($cookie,true); // assoc: true
	}

	$state = file_get_contents("sandbox/state.json");
	$state = json_decode($state, true); // assoc: true

	if (   !isset($state)
		|| !isset($state['endTime']) 
		|| $state['endTime'] < time() 
		|| areFinal()) {
			// give new expire time to client
			$state['endTime'] = time() + $PERIOD_S;
			$state['startTime'] = time();
			$state['mode'] = "live";
			$state['key'] = GUID();
			file_put_contents("sandbox/state.json",json_encode($state)); // rewrite state info, for new 
	} elseif ( isset($_COOKIE['pcii']) 
			&& $state
			&& $state['key']
			&& $cookie['key'] != -1
			&& $cookie['key'] == $state['key'] 
	) {
		// this is a return coder
		$state['mode'] = "live";
	} else {
		$state['mode'] = "queued";
		$state['key'] = -1; // don't give queued clients the key
	}

	if (isset($cookie['score'])) { 
		$state['score'] = $cookie['score'];
	} else {
		// no cookie so zero score
		$state['score'] = 0;
	}
	if (isset($cookie['name'])) { 
		$state['name'] = $cookie['name'];
	} else {
		$state['name'] = '';
	}
	//var_dump($state);
	//return;

	setcookie("pcii", json_encode($state), time() + 30*24*60*60);
?>

<html>
	<style type="text/css">
		@import url(jquery-ui.css );
		@import url(main.css);
	</style>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title>Coder</title>
	<script type="text/javascript" src="jquery-1.10.1.js"></script>
	<script type="text/javascript" src="jquery-ui.js"></script>
	<script type="text/javascript" src="nopaste.js"></script> 
	<script type="text/javascript" src="diff.js"></script>
	<script type="text/javascript" src="common.js"></script>
	<script type="text/javascript" src="scoreing.js"></script>
</head>
<body onload='document.getElementById("code").focus();'>
	
	<!--<span>server state: <?php var_dump($state); ?><span><br><br>-->
    <div id="header"><img src="images/header_bar.jpg" width="1024" height="59" alt="header graphic"><p class="logo"><a href="index.php" title="home">permanent deviation</a></p></div>
<div id="wrapper"> 	
<p class="labels"><span>Username: <input type="text" id="name" name="name" onBlur="nameSet()"></span><br>
	<span>Score: </span><span id="score" class="labelresults"></span><br>
	<span>You are: </span><span id="state" class="labelresults"></span><br>
	<span>Time: </span><span id="time" class="labelresults">...</span></p>
             <p>   <button id="play"   onclick="play();">Play</button>
				<button id="stop"   onclick="stop();">Stop</button>
             <button id="submit" onClick="submit();">Submit</button>
                <button id="reload" onClick="location.reload();">Try Code</button>
				<button id="reset"  onclick="resetCode();">Start Fresh</button>
				<button id="replay">Replay Lastest Coding</button>
				</p>
                <textarea id='code' class='unselectable' readyonly="readonly" onselectstart='return false;' onpaste='return false;' ondragstart='return false;'></textarea>
			
					<div id='sketch-container' class="canwrapper"><canvas id='sketch'></canvas></div>
    			<textarea class="disabled" id="output" readonly>None.</textarea> </div>
			
			<a style="display:none" id="sessionLink"  href="replay.php?startTime=<?php echo $state['startTime'];?>&endTime=<?php echo $state['endTime'];?>">session replay link</a>
		
<script type="text/javascript" src="processing/processing.min.js"></script>
<script type="text/javascript" src="helper/jsbeautify.js"></script> 
<script type="text/javascript" src="helper/processing-helper.js"></script> 
<script type="text/javascript">
		// JavaScript Globals

		var SCORE_NEEDED = 50;
		var codeSize = -1;
		var codeChangedEnoughtToAllowSubmit = false;
		var commentsAndWhiteSpace = new RegExp('/s+|(//.*$)','gm');
		var g_previousCode="";
		var g_previousTestCode="";

		function resetOk() {
			return (state['score'] >= SCORE_NEEDED && state['mode'] == 'live');
		}

		function updateCodeSize(data) {
			////console.log(data); // DEBUG
			codeChangedEnoughtToAllowSubmit = false;
			if (data) {
				g_previousTestCode = g_previousCode = unescape(data["code"]);
				codeSize = g_previousCode.length;
			} else {
				codeSize = -1;
			}
			console.log("::updateCodeSize, datalength is:" + codeSize); // DEBUG
		}

		function scoreAddition(frag) {
			var ret = 0;
			//console.log("Code Frag: " + frag);
			var words = frag.split(/[$\n\t\s()\-,\d;\{\}]+/);
			////console.log("Previous Words...");
				////console.log(previousWords);
			ret += 2*countReservedWords(frag);
			ret += countNumbers(frag);
			ret += countProcessingSpecial(frag);
			ret += 2*countComments(frag);
			return ret;
		}

		function codeToPoints() {
			var points = 0; // return value
			var codeEl = document.getElementById("code");
			var code = codeEl.value;

			var diff = JsDiff.diffChars(g_previousTestCode, code);
			g_previousTestCode = code;

			diff.forEach(function(part) {
				if (part.added) {
					var score =  scoreAddition(part.value);
					//console.log("Added...[" + part.value + "] : " + score + " points");
					points += score;
				} 
				/*
				else if (part.removed) {
					//console.log("Removed...");
					//console.log(part);
				} 
				else {
					// common
					////console.log("common..."); console.log(part);
				}
				*/
			});
			//if (points > 0) console.log("Run Sketch Success Result! YOU POINTS: " + points); 
			//console.log("::codeToPoints new points, " + points);
			return points;
		}

		function upload(step, callback_fn) {
			console.log("upload(...)");
			if (state == undefined) {
				console.log("upload() balked no state");
				return;
			}
			if (state['mode'] != "live" || state['key'] == -1) { 
				console.log("upload() balked not live | no key -1");
				return;
			}
			if (codeSize == -1) {
				console.log("upload() balked codeSize -1");
				return;
			}

			var code = document.getElementById("code").value;
			var name = document.getElementById("name").value;

			//console.log("code.length: " + code.length);
			//console.log("codeSize: " + codeSize);
			if ( code.length < codeSize-20 || code.length > codeSize+20 ) {
				if (!resetOk()) {
					download(updateCodeSize);
					alert("Sorry, Changes of Code larger than (40 char), are prevented (so people can see your Write the code");
					return;
				} else {
					state['score'] = 0;
					saveStateToCookie();
					console.log("score reset to 0");
				}
			}
			
			$.post('upload.php', 
				'{'															+ '\n' +
					'"step" :  "' + step       							 	+ '"\n'+
					',"name" :  "' + name       						 	+ '"\n'+
					',"time" :  ' + Math.floor(new Date().getTime()/1000) 	+ "\n" +
					',"score" :  ' + state['score']  					 	+ "\n" +
					',"endTime" :  ' + state['endTime']					  	+ "\n" +
					',"code":  "' + escape(code) 							+ '"\n'+
				"}\n" ,
				function(data) {
					//console.log("upload sucess");
					//console.log(data);
					codeSize = code.length;
					if (callback_fn) {
						callback_fn(data);
					} else {
					}
				}
			);
		}

		function updateSessionLink(data) {
			var url = href("replay", {'startTime' : state['startTime'], 'endTime' : data['uploadTime'] });
			state['uploadTime'] = data['uploadTime'];
			el = document.getElementById("sessionLink");
			if (!el) {
				console.warn("Can't Find sessionlink Element");
				return;
			}
			el.href = url;
			var addThisCont = document.getElementById("addthis_container");
			if (addThisCont) {
				addThisCont.setAttribute("addthis:url", url);
				//console.log("Updated addThis to:...");
				//console.log(addThisCont);
			} else {
				//console.log("can't fine addthis_container");
			}
		};

		function finalUpload() {
			upload("final", function(data) {
				//console.log("Final Upload Result:... ");
				data = $.parseJSON(data);
				//console.log(data);
				//console.log({ 'startTime' : state['startTime'], 'endTime' : data['uploadTime'] });
				updateSessionLink(data);
				state['mode'] = 'queued';
				submitTime = state['endTime'] = data['uploadTime'];
			});			
		}

		var submitTime = -1;
		function submit() {
			updateSessionLink({ uploadTime: time() });
			if (!codeChangedEnoughtToAllowSubmit) {
				//console.log("Balked on submit, no new code");
				return;
			}
			document.getElementById("output").innerHTML = "Running...";
			runSketch(function(e) {
				// call back error
				//console.log("Code Error:...");
				//console.log(e);
				var codeEl = document.getElementById("code");
				g_previousTestCode = codeEl.value; // wipe out chance to get score with those changes
				finalUpload();
			}, function(d) {
				state['score'] += codeToPoints();
				console.log("About to Upload");
				console.log(state);
				saveStateToCookie();
				finalUpload();
			});
		}

		function play() {
			runSketch(function(e) {
				// call back error
				//console.log("Code Error:...");
				//console.log(e);
				upload("test");
				//console.log(":(");
				var codeEl = document.getElementById("code");
				g_previousTestCode = codeEl.value; // wipe out chance to get score with those changes
			}, function() {
				// call back sucess
				state['score'] += codeToPoints();
				saveStateToCookie();
				upload("test");
			});
		}

		function stop() {
			upload("stop");
			stopSketch();
		}

		function canTryAgain() {
			var b_canTryAgain  = 
				(state['mode'] != 'live' && state['key'] == -1 && state['endTime'] < time() )
				// time expires
				||
				(state['mode'] != 'live' && state['key'] != -1 && (submitTime + RETRY_DELAY < time()))
				// we submited and then have to wait RETRY_DELAY to give others a chance
				|| 
				(state['mode'] == 'queued' && lastFrame && lastFrame['step'] == "final" && (submitTime + RETRY_DELAY < time()) && (lastFrame['time'] + 10 < time()))
				// we are watching and see a final step, and have not submitted lately, and final step is old
				;
			////console.log(state);
			return b_canTryAgain;
		}

		var watchingTime = -1;
		var lastFrame = null;
		function watcher_callback(newFrame) {
			if (newFrame['time'] == watchingTime) return; // got the same frame
			lastFrame = newFrame;
			watchingTime = newFrame['time'];

			var scoreEl = document.getElementById("score");
			var stateEl = document.getElementById("state");
			var timeEl = document.getElementById("time");

			scoreEl.innerHTML = state['score'];

			var codeEl = document.getElementById("code");
		
			if	(newFrame['step'] == 'test' || newFrame['step'] == 'final') {
				if (stateEl) {
					stateEl.innerHTML = "Watching: " +  
						((newFrame['step'] == 'test') 
						?  "Running Code" : "Final Code Running");
				}
				//console.log("Running Sketch");
				runSketch();
			} else if (newFrame['step'] == 'stop') {
				if (stateEl) {
					stateEl.innerHTML =  "Watching: Code Stopped";
				}
				//console.log("Stopping Sketch");
				stopSketch();
			} else {
				if (stateEl) stateEl.innerHTML = "Watching: Code Altered";
			}
		}

		function updatePage() {
			var scoreEl = document.getElementById("score");
			var stateEl = document.getElementById("state");
			var timeEl = document.getElementById("time");

			scoreEl.innerHTML = state['score'];

			var timeRemaining = calcRemainingTime(state['endTime']);

			if (timeRemaining >= 0 ) {
				timeEl.innerHTML = durToHMS(timeRemaining) + " remaining";
			}

			var codeEl = document.getElementById("code");

			if (state['mode'] == 'live' && timeRemaining <= 0) {
				//console.log("time ran out, queueing"); // DEBUG
				state['mode'] = 'queued';
				submit();
				timeEl.innerHTML = "TIMES UP";
			} else if (state['mode'] == 'queued') {
				download(watcher_callback); // watch
			}

			if (state['mode'] == 'live') {
				codeEl.className = "normal";
				codeEl.readOnly = false ;
				stateEl.innerHTML = 'LIVE CODING';
			} else {
				codeEl.className = "disabled";
				codeEl.readOnly = true ;
			}

			$('#play').button("option", "disabled", 
				!(state['mode']=='live') );
			$('#submit').button("option", "disabled",
				!(state['mode']=='live' && codeChangedEnoughtToAllowSubmit) );
			$('#reset').button("option", "disabled", 
				!resetOk() );
			$('#reload').button("option", "disabled",
				!(canTryAgain()) );
			$('#stop').button("option", "disabled", 
				!(state['mode']=='live') );
			$('#replay').button("option", "disabled",
			   !(state['mode']!='live'));
		}

		function resetCode() {
			var codeEl = document.getElementById("code");
			codeEl.value = DEFAULT_CODE;
			state['score'] = 0;
			saveStateToCookie();
			upload("update");
		}

</script>
<script type="text/javascript">
	initCookieToState();
	$(document).ready(function () {
			$('button').button();
			$('button').button("option", "disabled", true );
			$('#play').button("option", "disabled", false );
			$('#name').prop('disabled',false);

			$('#replay').click( function() {
				//location.href = href("replay"); // last 300 steps
				var params = { 'startTime' : state['startTime'] };
				if (state['uploadTime']) {
					params.endTime = state['uploadTime'];
				} else {
					params.endTime = state['endTime'];
				}
			 	location.href = href("replay", params);
			});
			$(window).bind('beforeunload', function(){
				if (state['mode'] == 'live' && codeChangedEnoughtToAllowSubmit) {
					return "You have NOT submited your code yet.";
				} 
			});

			$('#code').keyup(function(evt) {
				if(isNavKey(evt.which)) {
					////console.log(evt.keyCode);
					////console.log("Ignoring Nav key " + evt.which);
					return;
				}
				//if code and g_previousCode is significantly different, upload
				var codeEl = document.getElementById("code");
				var code = codeEl.value;
				if (!codeChangedEnoughtToAllowSubmit && code.replace(commentsAndWhiteSpace," ") == 
								g_previousCode.replace(commentsAndWhiteSpace," ")) {
					//console.log("no change, submit greyed");
				} else {
					codeChangedEnoughtToAllowSubmit = true;
				}


				upload('update');
			});

			console.log("Initial Download - Started");
			download(updateCodeSize); // initial download for lastest code
			if (state['name']) {
				var nameEl = document.getElementById("name");
				nameEl.value = state['name'];
			}
			////console.log("Expire Time: " + new Date(state['endTime']*1000).toString());

			updatePage();
			var g_timer = setInterval("updatePage();",1000);
	});
</script>
<!-- AddThis Button BEGIN -->
<p>
<div id="addthis_container" class="addthis_toolbox addthis_floating_style addthis_20x20_style" style="right:3px; top:65px;">
<a class="addthis_button_facebook"></a>
<a class="addthis_button_twitter"></a>
<a class="addthis_button_email"></a>
<a class="addthis_button_google_plusone_share"></a>
</div>
	<script type="text/javascript">
	var addthis_config = {
		data_track_addressbar : true,
	};
	var addThisCont = document.getElementById("addthis_container");
	var el = document.getElementById('sessionLink');
	if (!el) {
		console.warn("Can't Find sessionlink Element");
	} else { 
		//console.log("Add This Replay URL: " + el.href);
		addThisCont.setAttribute("addthis:url", el.href);
	}
	</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52dd6b4d43bce6fc"></script>
</p>
<!-- AddThis Button END -->
</body>
</html>
