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
	
		function replay_callback(newFrame) {
			//console.log(newFrame);
			var name = document.getElementById("name");
			var stateEl = document.getElementById("state");
			var time = document.getElementById("time");
			var score = document.getElementById("score");
			var secondsAgo = timeDiff_sec(newFrame['time']);

			name.innerHTML = newFrame['name'];
			score.innerHTML = newFrame['score'];

			//time.innerHTML = durToFriendlyString(secondsAgo);
			//time.innerHTML = "" + secondsAgo;
			time.innerHTML = durToHMS(secondsAgo);
		
			if	(newFrame['step'] == 'test' || newFrame['step'] == 'final') {
				if (stateEl) {
					stateEl.innerHTML =  
						(newFrame['step'] == 'test') 
						?  "Running Code" : "Final Code Running";
				}
				console.log("Running Sketch");
				runSketch();
			} else if (newFrame['step'] == 'stop') {
				console.log("Stopping Sketch");
				stopSketch();
			} else {
				if (stateEl) stateEl.innerHTML = "Code Altered";
			}
		}

		function updateAnimation() {
			//console.log("Frame Time: " + frameTimes[frameIndex]);
			download(replay_callback, frameTimes[frameIndex] );
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
<div id="header"><img src="images/header_bar.jpg" alt="header graphic"><p class="logo"><a href="index.php" title="home">permanent deviation</a></p></div>
        <div id="wrapper">
	<!--<h3>REPLAY LAST SESSION</h3>-->
	<p class="labels"><span>Coding Happened: </span> <span id="time" class="labelresults">...</span> ago<br>
	<span>Coder was: <span id='name' class="labelresults">...</span><br>
	<span>Coder's Score then: <span id='score' class="labelresults">...</span><br>
	<!--<span>Status: </span><span id=state class="labelresults">...</span>--></p>

					<p><button id="watchLive">Watch Live Coding!</button>
					<button id="tryCode" >Try to Code</button></p>
                    
			
				<textarea id="code" class="disabled" rows="25" cols="60" readonly></textarea>
		
				<div id='sketch-container' class="canwrapper"><canvas id='sketch' class='editor'></canvas>
				</div>
			
    			<textarea class="disabled" rows="10" cols="80" id="output" readonly>None.</textarea><p><a href="http://permanentdeviation.com/reference.html" target="_blank"><img src="images/reference.png" width="204" height="25" alt="link to reference"></a></p>
                </div>
			
	<script type="text/javascript" src="processing/processing.min.js"></script>
	<script type="text/javascript" src="helper/jsbeautify.js"></script> 
	<script type="text/javascript" src="helper/processing-helper.js"></script> 
	<script type="text/javascript" >
				console.log(location.href);
				checkForLiveCoding();
				setInterval("checkForLiveCoding();",10*1000);
				var args = getQueryParams(document.location.search);
				$.getJSON(appendArgs('phpReplayTime.php', args), function(range) { 
					console.log("Got Frames Catalog:..");
					//console.log(range);
					frameTimes = range;
					console.log("Replay Duration: " + range.length + " frames");
					if (range.length) { // got replay frames, start animation
						updateAnimation();
						timer = setInterval("updateAnimation();",1000);
					}
				});
	</script>
<p>
<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_floating_style addthis_20x20_style" style="right:3px; top:65px;"">
<a class="addthis_button_facebook"></a>
<a class="addthis_button_twitter"></a>
<a class="addthis_button_email"></a>
<a class="addthis_button_google_plusone_share"></a>
</div>
<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52dd6b4d43bce6fc"></script>
<!-- AddThis Button END -->
</p>
</body>
</html>
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
