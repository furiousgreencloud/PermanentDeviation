<!DOCTYPE html>
<html>
	<style type="text/css">
		@import url(jquery-ui.css );
		@import url(main.css);
	</style>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<script type="text/javascript" src="jquery-1.10.1.js"></script>
	<script type="text/javascript" src="jquery-ui.js"></script>
	<script type="text/javascript" src="common.js"></script>
	<script type="text/javascript">
		var frameTimes;
		var frameTime = 0;
		var frameIndex = 0;
		var timer;
		function fillInPage_callback(newFrame) {
			//console.log(newFrame);
			var name = document.getElementById("name");
			var state = document.getElementById("state");
			var time = document.getElementById("time");
			var score = document.getElementById("score");
			var secondsAgo = timeDiff_sec(newFrame['time']);

			name.innerHTML = newFrame['name'];
			score.innerHTML = newFrame['score'];

			//time.innerHTML = durToFriendlyString(secondsAgo);
			//time.innerHTML = "" + secondsAgo;
			time.innerHTML = durToHMS(secondsAgo);
		
			if	(newFrame['step'] == 'test' || newFrame['step'] == 'final') {
				state.innerHTML =  (newFrame['step'] == 'test') ?
				   	"Running Code" : "Final Code Running";
				console.log("Running Sketch");
				runSketch();
			} else {
				state.innerHTML = "Code Altered";
			}
		}

		function updateAnimation() {
			//console.log("Frame Time: " + frameTimes[frameIndex]);
			download(fillInPage_callback, frameTimes[frameIndex] );
			frameIndex++;
			if (frameIndex >= frameTimes.length) {
				clearInterval(timer);
				console.log("Done");
			}
		}

		function checkForLiveCoding() {
			$.getJSON("state.php",function(state) {
				//console.log("EndTime: " + state['endTime']); 
				//console.log("Now: " + time()); 
				$someoneIsLive = (state['endTime'] > time());
				$('#watchLive').button("option", "disabled", !$someoneIsLive );
				$('#tryCode').button("option", "disabled", $someoneIsLive );
			});
		}


		$(document).ready(function () {

			$('button').button();
			$('button').button("option", "disabled", true);

			$('#watchLive').click( function() {
				location.href = href("viewer");
			});

			$('#tryCode').click( function() {
				location.href = href("coder");
			});

		});
	</script>
</head>
<body>
	<h3>Replay</h3>
	<span>Coding Happended: </span> <span id="time">...</span> ago<br>
	<span>Coder was: <span id='name'>...</span><br>
	<span>Coder's Score then: <span id='score'>...</span><br>
	<span>Status: </span><span id=state>...</span><br>
	<table>
		<tr valign="top">	
			<tr>
				<td colspan=2>
					<button id="watchLive">Watch Live Coding!</button>
					<button id="tryCode" >Try Code</button>
				</td>
			</tr>
			<td>
				<h3>Code</h3>
				<textarea id="code" class="disabled" rows="25" cols="60" readonly="readonly"></textarea><br>
			</td>
			<td>
				<h3>Canvas</h3>
				<div id='sketch-container'>
					<canvas id='sketch'></canvas>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan = 2>
				<h3>Errors</h3>
    			<textarea class="disabled" rows="10" cols="80" id="output" readonly="readonly">None.</textarea>
			</td>
		</tr>
	</table>
	<script type="text/javascript" src="processing-1.4.1.min.js"></script>
	<script type="text/javascript" src="helper/jsbeautify.js"></script> 
	<script type="text/javascript" src="helper/processing-helper.js"></script> 
	<script type="text/javascript" >
				checkForLiveCoding();
				setInterval("checkForLiveCoding();",10*1000);
				$.getJSON('getReplayTimes.php', function(range) {
					//console.log("Got Frames Catalog:..");
					//console.log(range);
					frameTimes = range;
					console.log("Replay Duration: " + range.length + " sec");
					if (range.length) { // got replay frames, start animation
						updateAnimation();
						timer = setInterval("updateAnimation();",1000);
					}
				});


	
	</script>
</body>
</html>
