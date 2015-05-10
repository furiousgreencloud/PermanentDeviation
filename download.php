<?php
require 'constants.php';
/*
	echo "GET:...";
	var_dump($_GET);


	echo "POST:...";
	var_dump($postData);
 */

	function returnLatest() {
		GLOBAL $db;
		$q = "SELECT * FROM frameTable ORDER BY timeCode DESC LIMIT 1;";
		$result = $db->query($q);
		if ($result) {
			//var_dump($result);
			$row = $result->fetch_array(MYSQLI_ASSOC);
			//var_dump($row);
			echo urldecode($row['frame']);
		}
		$db->close();
		return;
		
		// raw style: -- works great
		// copy("sandbox/latest.json","php://output");
		// flush("php://output");
		// raw style - end
		return;
	}

	$postData = file_get_contents("php://input");
	if (!empty($_GET)) {
		$json=$_GET;
	} else if (!empty($postData)) {
		$json = json_decode($postData,true); // true: assoc mode
	} else { // just send latest
		returnLatest();
	}
	   
	if ($json) { 
		//echo "JSON good\n";
		if ($json['frame'] == 'latest') {
			returnLatest();
		} else if (is_numeric($json['frame'])) {
			/* // -- FILE STYLE -- //
			// echo("FILENAME sandbox/frame-" . $json['frame'] .".json\n");
			$selectedFrameFilename =  "sandbox/frame-".$json['frame'].".json";
			if (file_exists($selectedFrameFilename)) {
				copy($selectedFrameFilename,"php://output");
				flush("php://output");
			} else {
				echo "no such frame";
			}
			 */ // -- END FILE STYLE --/
			$q = "SELECT * from frameTable WHERE timeCode = ".$json['frame'].";";
			$result = $db->query($q);
			if ($result) {
				$row = $result->fetch_array(MYSQLI_ASSOC);
				echo urldecode($row['frame']);
			} else {
				echo "Query: $q, FAILED, result...\n";
				var_dump($result);
			}
			return;
		} else {
			echo "i don't understand JSON:...\n" ;
			var_dump($json);
			return;
		}
	} else {
		/*
		echo "Get/Post is bad JSON\n";
		echo "GET That Produced Bad JSON is:...\n";
		var_dump($_GET);
		echo "POST/INPUT:...\n";
		var_dump(file_get_contents("php://input"));
		 */
		returnLatest();
	}
?>
