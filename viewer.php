<!DOCTYPE html>
<html>
	<style type="text/css">
		@import url(main.css);
		@import url(jquery-ui.css );
	h4 {
	font-size: 16px;
}
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
				//console.log("same download times"); // DEBUG
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
		document.addEventListener("keydown", function(e) {
			if (e.keyCode == 13 && e.metaKey) {
				toggleFullScreen();
			}
		}, false);

	</script>
</head>

<body>
<div id="header"><img src="images/header_bar.jpg" alt="header graphic"><p class="logo"><a href="index.php" title="home">permanent deviation</a></p></div>
<div id="wrapper">
<!--<h3>VIEWER</h3>-->
	<p class="labels"><span>Coder is: <span id='name' class="labelresults">...</span><br>
	<span>Coder's Score: <span id='score' class="labelresults">...</span><br>
	<span>Status: </span><span id=state class="labelresults">...</span><br>
	<span>Time: </span> <span id="time" class="labelresults">...</span></p>
	
				<p><button id="replay">Replay Latest</button>
				<button id="tryCode">Try Code</button></p>
		
 	
        
				<textarea id="code" class="disabled" rows="25" cols="60" readonly></textarea>
			
				<div id='sketch-container' class="canwrapper">
                <canvas id='sketch' class='editor' ></canvas>
				</div>
			
    			<textarea class="disabled" rows="10" cols="80" id="output" readonly>None.</textarea>
                </div>
			
            
	<script type="text/javascript" src="processing/processing.min.js"></script>
	<script type="text/javascript" src="helper/jsbeautify.js"></script> 
	<script type="text/javascript" src="helper/processing-helper.js"></script> 
	<script type="text/javascript" >
		update();
		setInterval("update();",1000);
	</script>
</body>
</html>

