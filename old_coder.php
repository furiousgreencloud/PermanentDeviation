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
		//var_dump($cookie);
	}

	$state = file_get_contents("sandbox/state.json");
	$state = json_decode($state, true); // assoc: true

	if (   !isset($state)
		|| !isset($state['endTime']) 
		|| $state['endTime'] < time() 
		|| areFinal()) {
			// give new expire time to client
			$state['endTime'] = time() + $PERIOD_S;
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
	<script type="text/javascript" src="common.js"></script>
</head>
<body id='body' onload='document.getElementById("code").focus();'>
	<h3>Coder</h3>
	<!--<span>server state: <?php var_dump($state); ?><span><br><br>-->
	<span>Username: <input type="text" id="name" name="name" onblur="nameSet()"></span><br>
	<span>Score: </span><span id="score"></span><br>
	<span>You are: </span><span id="state"></span><br>
	<span>Time: </span><span id="time">...</span><br>
	<table>
		<tr>
			<td colspan=2>
				<button id="test"   onclick="test();">Test</button>
				<button id="reset"  onclick="resetCode();">Start Fresh</button>
				<button id="replay">Replay Lastest Coding</button>
				<button id="reload" onclick="location.reload();">Try Again</button>
				<button id="submit" onclick="submit();">Submit</button>
			</td>
		</tr>
		<tr valign="top">	
			<td>
				<h3>Code</h3>
				<textarea id='code' class='disabled' readyonly="readonly" onpaste='return false;'></textarea>
			</td>
			<td>
				<h3>Canvas</h3>
				<div id='sketch-container'>
					<canvas id='sketch'></canvas>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<h3>Errors</h3>
    			<textarea class="disabled" id="output" readonly="readonly">None.</textarea>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<a id="sessionLink" href="">session replay link</a>
			</td>
		</tr>
	</table>
<script type="text/javascript" src="procesing/processing.min.js"></script>
<script type="text/javascript" src="helper/jsbeautify.js"></script> 
<script type="text/javascript" src="helper/processing-helper.js"></script> 
<script type="text/javascript">
		// JavaScript Globals

		var SCORE_NEEDED = 2;
		var codeSize = -1;
		var codeChangedEnoughtToAllowSubmit = false;
		var commentsAndWhiteSpace = new RegExp('/s+|(//.*$)','gm');
		var previousCode="";
		var startTime;

		function resetOk() {
			return (state['score'] >= SCORE_NEEDED && state['mode'] == 'live');
		}

		function updateCodeSize(data) {
			//console.log(data); // DEBUG
			codeChangedEnoughtToAllowSubmit = false;
			if (data) {
				previousCode = unescape(data["code"]);
				codeSize = previousCode.length;
				//console.log("::updateCodeSize, datalength is:" + codeSize); // DEBUG
			} else {
				codeSize = -1;
				console.log("::updateCodeSize, NO CODE"); // DEBUG
			}
		}

		$('#code').keyup(function(evt) {
			console.log(evt.keyCode);
			if(isNavKey(evt.which)) {
				console.log("Ignoring Nav key " + evt.which);
				return;
			}
			//if code and previousCode is significantly different, upload
			var code = document.getElementById("code").value;
			if (!codeChangedEnoughtToAllowSubmit && code.replace(commentsAndWhiteSpace," ") == 
							previousCode.replace(commentsAndWhiteSpace," ")) {
				console.log("no change, submit greyed");
			} else {
				codeChangedEnoughtToAllowSubmit = true;
			}
			upload('update');
		});

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

			if ( code.length < codeSize-40 || code.length > codeSize+40 ) {
				if (!resetOk) {
					download(updateCodeSize);
					alert("Sorry, Changes of Code larger than (80char), are prevented (so people can see your Write the code");
					return false;
				} else {
					state['score'] = 0;
					saveStateToCookie();
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
						console.log("upload sucess");
						console.log(data);
						codeSize = code.length;
						if (callback_fn) callback_fn(data);
					}

				);
		}

		function saveStateToCookie() {
			console.log("Cookie is :...");
			console.log(state);
			document.cookie = "pcii=" + encodeURIComponent(JSON.stringify(state));
		}

		function submit() {
			if (!codeChangedEnoughtToAllowSubmit) {
				console.log("Balked on submit, no new code");
				return;
			}
			document.getElementById("output").innerHTML = "";
			runSketch();
			if (document.getElementById("output").innerHTML == "None.") {
				state['score']++;
				saveStateToCookie();
			}
			upload("final", function(data) {
				console.log("Final Upload Result:... ");
				data = $.parseJSON(data);
				console.log(data);
				console.log({ 'startTime' : startTime-2, 'endTime' : data['uploadTime'] });
				url = href("replay", 
						{ 'startTime' : startTime-2,  'endTime' : data['uploadTime'] });
				el = document.getElementById("sessionLink");
				el.href = url;
			});
			state['mode'] = 'queued';
			state['endTime'] = time();
		}

		function test() {
			upload("test");
			runSketch();
		}

		function updatePage() {
			var scoreEl = document.getElementById("score");
			var stateEl = document.getElementById("state");
			var timeEl = document.getElementById("time");

			scoreEl.innerHTML = state['score'];

			var timeRemaining = calcRemainingTime(state['endTime']);
			if (timeRemaining >= 0) {
				timeEl.innerHTML = durToHMS(timeRemaining) + " remaining";
			} else {
				timeEl.innerHTML = "TIMES UP";
			}
			var codeEl = document.getElementById("code");

			if (state['mode'] == 'live' && timeRemaining <= 0) {
				console.log("time ran out, queueing"); // DEBUG
				state['mode'] = 'queued';
				submit();
			} else if (state['mode'] == 'queued') {
				download(updateCodeSize); // watch
			}

			if (state['mode'] == 'live') {
				codeEl.className = "normal";
				codeEl.readOnly = false ;
				stateEl.innerHTML = 'LIVE';
			} else {
				codeEl.className = "disabled";
				codeEl.readOnly = true ;
				stateEl.innerHTML = "WATCHING";
			}

			$('#test').button("option", "disabled", 
				!(state['mode']=='live') );
			$('#submit').button("option", "disabled",
				!(state['mode']=='live' && codeChangedEnoughtToAllowSubmit) );
			$('#reset').button("option", "disabled", 
				!resetOk() );
			$('#reload').button("option", "disabled",
				!(state['mode']=='live' && timeRemaining < 30) );
			$('#reload').button("option", "disabled",
				!(state['mode']!='live') );
		}

		function resetCode() {
			var codeEl = document.getElementById("code");
			codeEl.value = "void setup() {\n  // setup code here\n}\n\nvoid draw() {\n  // animation code here\n}";
			upload("update");
			state['score'] = 0;
			saveStateToCookie();
		}


</script>
<script type="text/javascript">
	var state = eval(decodeURIComponent(document.cookie));
	var startTime = time();
	console.log("cookie/state=...");
	console.log(state);
	$(document).ready(function () {
			$('button').button();
			$('button').button("option", "disabled", true );
			$('#test').button("option", "disabled", false );
			$('#replay').button("option", "disabled", false );

			$('#replay').click( function() {
				location.href = href("replay");
			});

			download(updateCodeSize); // initial download for lastest code
			if (state['name']) {
				var nameEl = document.getElementById("name");
				nameEl.value = state['name'];
			}
			//console.log("Expire Time: " + new Date(state['endTime']*1000).toString());

			updatePage();
			var g_timer = setInterval("updatePage();",1000);
	});
</script>
</body>
</html>
