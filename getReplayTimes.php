<?php
	//load latest,
	//figure out time 5 minutes before that
	//send client start time for replay, avoid long gaps

	require('constants.php');

	if (!empty($_GET)) {
		$args=$_GET;
	} else if (!empty($postData)) {
		$postData = file_get_contents("php://input");
		$args = json_decode($postData,true); // true: assoc mode
	} 

	$q = "SELECT timeCode FROM frameTable";
	$q =  $q . " ORDER BY timeCode DESC";
	$q =  $q . " LIMIT $MAX_REPLAY_TIME";

	if (isset($args)) {
		//var_dump($args);
		$q = 'SELECT timeCode FROM frameTable ';
		
		if (isset($args['endTime']) || isset($args['startTime'])) {

			$q = $q . " WHERE ";
			if (isset($args['endTime'])) {
				$q  .= "timeCode <= " . $args['endTime'];
			}

			if (isset($args['startTime']) && isset($args['endTime'])) {
				$q  .= " AND ";
			}
			if (isset($args['startTime'])) {
				$q  .= "timeCode >= " . $args['startTime'];
			}
		}

		$q =  $q." ORDER BY timeCode DESC";
		if (!isset($args['startTime']) || !isset($args['endTime'])) {
			$q = $q. " LIMIT $MAX_REPLAY_TIME";
		}
	}
	//echo "DOING: $q\n";
	$result = $db->query($q);
	//var_dump($result);

	$frameList = array();
	for($i = 0; $i < $result->num_rows; $i++) {
		//var_dump($result->fetch_object());
		$row = $result->fetch_object();
		array_unshift($frameList,$row->timeCode);
	}
	echo json_encode($frameList);
?>
