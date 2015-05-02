<!DOCTYPE html>
<html>
	<style type="text/css">
		@import url(jquery-ui.css );
		@import url(main.css);
	</style>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title>Welcome</title>
	<script type="text/javascript" src="jquery-1.10.1.js"></script>
	<script type="text/javascript" src="jquery-ui.js"></script>
	<script type="text/javascript" src="common.js"></script>
</head>
<body>
<div id="header"><img src="images/header_bar.jpg" width="1024" height="59" alt="header graphic"><p class="logo">permanent deviation</p></div>
	<h3>WELCOME</h3>
	<!--<span>server state: <?php var_dump($state); ?><span><br><br>-->
	<p>&nbsp;</p>
	<p><span class="labels">Username: 
    <input type="text" id="name" name="name">
	  </span><br>
	  <button id="continue" >Yes, I'll Code!</button>
	  <button id="watch">Err,Can I Just Watch?</button>
</p>
<script type="text/javascript">
// JavaScript Globals


function updateContinueButton() {
	var el = document.getElementById('name');
	var canContinue =  !isBlank(el.value);
	$('#continue').button("option", "disabled", !canContinue);
	return canContinue;
}

// Handlers

$(document).ready(function () {
	$('#continue').click( function() {
		location.href = href("coder");
	});
	$('#watch').click( function() {
		location.href = href("replay");
	});

	$('input#name').blur( function(evt) {
		updateContinueButton();
		nameSet();
	});


	$('input#name').bind( {
		change: updateContinueButton,
		keyup: updateContinueButton,
		keypress : function(e) {
			if (e.which == 13) { // 13 is ENTER!
				nameSet();
				if (updateContinueButton()) location.href = href("coder");
			}
		}
	});

	/*

	$('input#name').change(function(evt) {
		console.log("CHANGE EVENT!");
		updateContinueButton(evt);
	});

	$('input#name').keyup(function(evt) {
		updateContinueButton(evt);
	});
	 */
	initCookieToState();
	$('button').button();
	if (state['name'] && state['name'].length > 0) {
		var nameEl = document.getElementById("name");
		nameEl.value = state['name'];
	}
	updateContinueButton();
});


</script>
</body>
</html>

