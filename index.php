<!DOCTYPE html>
<html>
	<style type="text/css">
		@import url(jquery-ui.css );
		@import url(main_home.css);
	
	
	a:link {
	color: #666;
	text-align: center;
}
    a:visited {
	color: #666;
}

    body {
	background-image: url(images/background02.png);
	background-repeat: no-repeat;
}
    </style>
    
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title>permanentdeviation.org</title>
	<script type="text/javascript" src="jquery-1.10.1.js"></script>
	<script type="text/javascript" src="jquery-ui.js"></script>
	<script type="text/javascript" src="common.js"></script>
</head>

<body >
<div id="homewrapper">
	<!--<span>server state: <?php var_dump($state); ?><span><br><br>-->
	<p><span class="labels">Username: 
    <input type="text" id="name" name="name">
	  </span>
  <p> <button id="continue" >Yes, I'll Code!</button>
	  <button id="watch">No, can I just watch?</button></p>
  <p>&nbsp;</p>
  <p>Learn more about this project and VIVO<a href="http://www.vivomediaarts.com/permanent-deviation/" target="_blank"> <strong>here</strong>.</a></p>
  <p>Hashtag: <strong>#vivopd</strong></p>
  <p>Creation and ongoing development by: <a href="http://www.desiringproductions.com" target="_blank">Julie Gendron</a>, <a href="http://www.furiousgreencloud.com" target="_blank">Brady Marks</a> and <a href="http://danielmclaren.com" target="_blank">Niel McLaren</a>.
  <p>Please send feedback or bugs to <a href="mailto:info@permanentdeviation.com"><strong>info@permanentdeviation.com</strong></a></p>
</div>
      
<div id="vivologo"><a href="http://vivomediaarts.com" target="_blank"><img src="images/vivo.png" width="133" height="95" alt="VIVO Logo"></a></div>
<div id="supportlogos"><img src="images/supporter_logos_s.png" width="600" height="95" alt="Funding Logos"></div>
      

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

