<?php
	$state = file_get_contents("sandbox/state.json");
	if (!isset($state)) {
		return false;
	}
	$state = json_decode($state, true); // assoc: true
	unset($state['key']); // never give out key
	echo json_encode($state);
?>



