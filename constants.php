<?php
	require 'private.php';
	$PERIOD_S = (60*4); // seconds
	$MAX_REPLAY_TIME_SPAN = (2*24*60*60); // two days
	$MAX_REPLAY_TIME = (5*60); // 5 min
	//$MAX_REPLAY_TIME = 10; // 5 min
	//
	// REMOVE MAGIC QUOTES, ala http://php.net/manual/en/security.magicquotes.disabling.php
	if (get_magic_quotes_gpc()) {
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				} else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
		unset($process);
	}

	function GUID()
	{
		if (function_exists('com_create_guid') === true)
		{
			return trim(com_create_guid(), '{}');
		}

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

	$host = "localhost";
	$user = "permanen_admin";
	$databaseName = "permanen_frameDB";
	$tableName = "frameTable";
	$db = new mysqli($host,$user,$pass,$databaseName);
?>
