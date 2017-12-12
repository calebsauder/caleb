<?php

chdir(dirname(__FILE__));

require_once("inc/init.inc.php");

$lb = "\n";

echo $lb.$lb;
echo "Opening network config...".$lb;
 
$network_config = get_networkConfig();

if (file_exists($cron_marker)) {
	echo "Cron already running...".$lb;
	
	$running_since = '';
	$fp = fopen($cron_marker,"r");
	$record = file($cron_marker);
	for ($n=0; $n<count($record); $n++) $running_since .= $record[$n];
	$running_since = (int)trim($running_since);
	if (($tstamp - $running_since) > 60) {
		echo "Cron has been running longer than 60 seconds...reset marker.".$lb;
		unlink($cron_marker);
	}
	else {
		echo "Cron has been running for ".($tstamp - $running_since)." seconds.".$lb;
	}
	
}
else {
	// prevent multi-threading
	$fp = fopen($cron_marker,"w");
	fwrite($fp,$tstamp);
	fclose($fp);
	
	if (is_array($network_config['connect'])) {
		echo "Request for connect queued".$lb;
		if (is_array($network_config['connect'])) {
			$network =  trim($network_config['connect']['network']);
			$password = trim($network_config['connect']['password']);
			if ($network == '') {
				echo "Error: Missing required param [network]".$lb;
			}
			elseif ($password == '') {
				echo "Error: Missing required param [password]".$lb;
			}
			else {
				echo "Attempting to connect to network...".$lb;
				echo "- Network: ".$network.$lb;
				echo "- Password: ".$password.$lb;
				set_wifi_network($network,$password);
			}
		}
		else {
			echo "Error: Expected array for connect config".$lb;
		}
		echo "Clearing network connect flag".$lb;
		$network_config['connect'] = false;
	}
	else {
		echo "No request for network connection.".$lb;
	}

	if ($network_config['monitoring']) {
		echo "User is monitoring".$lb;
		$ts = (int)$network_config['last_monitor'];
		if ($ts >= ($tstamp - 30)) { // last request was within 30 seconds
			echo "User monitor last requested ".($tstamp - $ts)." seconds ago.".$lb;
			echo "Scanning for available WiFi networks...";
			$rsp = get_wifi_networks();
			if ($rsp['response'] == 'success') {
				$network_config = $rsp['current_wifi_network'];
				echo "Success!".$lb;
				$names = array();
				foreach ($rsp['networks'] as $network){
					if ($network['SSID'] != '') array_push($names,$network['SSID']);
				}
				$names = array_unique($names);
				sort($names);
				$networks = $names;
			}
			else {
				echo "Error: ".$rsp['error'].$lb;
				$networks = array();
			}
			$network_config['networks'] = $networks;
			print_r($networks);
		}
		else {
			echo "It has been longer than 30 seconds than last request for user monitoring".$lb;
			$network_config['monitoring'] = false; // turn off monitoring
			$network_config['last_monitor'] = 0;
		}
	}
	else {
		echo "No user monitoring".$lb;
	}

	unlink($cron_marker);
	
	echo "Updating network config...".$lb;
	$fp = fopen($network_file,"w");
	fwrite($fp,json_encode($network_config));
	fclose($fp);
}

echo "exiting".$lb.$lb;
exit;

?>