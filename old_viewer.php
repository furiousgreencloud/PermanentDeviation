<!DOCTYPE html>
<html>
	<style type="text/css">
		@import url(main.css);
		@import url(jquery-ui.css );
	</style>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<script type="text/javascript" src="jquery-1.10.1.js"></script>
	<script type="text/javascript" src="common.js"></script>
	<script type="text/javascript" src="jquery-ui.js"></script>
	<script type="text/javascript">
		var download_data;
		function download_callback(data) {
			//console.log("Download_callback.data: ..");
			//console.log(data);
			var name = document.getElementById("name");
			var state = document.getElementById("state");
			var time = document.getElementById("time");
			var score = document.getElementById("score");
			var remainingTime = calcRemainingTime(data['endTime']);

			name.innerHTML = data['name'];
			score.innerHTML = data['score'];

			if (data['step'] == 'final') {
				time.innerHTML = "--:--:--";
				state.innerHTML = "Idle, This is the Coder's Final Submittion";
			} else if (remainingTime > 0) {
				time.innerHTML = durToHMS(remainingTime) + " remaining";
				state.innerHTML = "LIVE";
			} else {
				state.innerHTML = "Idle";
				time.innerHTML = durToHMS(-1 * remainingTime) + " since last action";
			}
			if ( download_data && download_data['time'] == data['time']) {
				// same step as last step
				// don't runSketch
			} else  {
				if	(data['step'] == 'test' || data['step'] == 'final') {
					console.log("runing Sketch()"); // DEBUG
					runSketch();
				}
			}
			download_data = data;
		}
		function update() {
			download(download_callback);
		}
		$(document).ready(function () {

			$('button').button();
			//$('button').button("option", "disabled", true);

			$('#replay').click( function() {
				location.href = href("replay");
			});

			$('#tryCode').click( function() {
				location.href = href("coder");
			});

		});
	</script>
</head>
<body id='wrapper'>
	<h3>Viewer</h3>
	<span>Coder is: <span id='name'>...</span><br>
	<span>Coder's Score: <span id='score'>...</span><br>
	<span>Status: </span><span id=state>...</span><br>
	<span>Time: </span> <span id="time">...</span><br>
	<table>
		<tr>
			<td colspan=2>
				<button id="replay">Replay Latest</button>
				<button id="tryCode">Try Code</button>
			</td>
		</tr>
		<tr valign="top">	
			<td>
				<h3>Code</h3>
				<textarea id="code" class="disabled" rows="25" cols="60" readonly></textarea><br>
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
    			<textarea class="disabled" rows="10" cols="80" id="output" readonly>None.</textarea>
			</td>
		</tr>
	</table>
	<script type="text/javascript" src="processing-1.4.1.min.js"></script>
	<script type="text/javascript" src="helper/jsbeautify.js"></script> 
	<script type="text/javascript" src="helper/processing-helper.js"></script> 
	<script type="text/javascript" >
		update();
		setInterval("update();",1000);
	</script>
</body>
</html>

