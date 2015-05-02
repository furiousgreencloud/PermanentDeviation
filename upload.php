<?php
	require 'constants.php';
/*
	// save only the code part in the the latest.pde
	$json = json_decode(file_get_contents("php://input"),true);
	if($json ) {
		echo "JSON good";
		// TODO bail from upload, if key does not match for secuirity reasons
	} else {
		echo "Post is bad JSON:\n" . file_get_contents("php://input");
		return;
	}
*/

	$time = time();
	$frame = file_get_contents("php://input");
	$frameBlob = urlencode($frame);
	$query = "REPLACE INTO $tableName (timeCode,frame) " 
		." VALUES ($time,\"$frameBlob\")";
	//echo "QUERY: $query\n\n";
	$result = $db->query($query);
	//var_dump($result);
	if ($result == FALSE) {
		echo $db->error;
		return;
	} 

	$frame = json_decode($frame, true); //assoc: true
	if (isset($frame) && $frame['step'] == 'final') {
		$state = file_get_contents("sandbox/state.json");
		if (!isset($state)) {
			return false;
		}
		$state = json_decode($state, true); // assoc: true
		unset($state['key']); 
		$state['endTime']=$time;
		$state['mode']='free';
		file_put_contents("sandbox/state.json",json_encode($state));
		//echo "Updated State:...";
		//var_dump($state);
	}
	$db->close();
	echo "{ \"uploadTime\": $time }";
?>

