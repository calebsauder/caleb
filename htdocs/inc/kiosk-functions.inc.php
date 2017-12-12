<?php

function get_networkConfig(){
	global $network_file, $network_config_default;
	$fp = fopen($network_file,"r");
	$record = file($network_file);
	$data = '';
	for ($n=0; $n<count($record); $n++) $data .= $record[$n];
	$data = trim($data);
	$network_config = @json_decode($data,true);
	if (!is_array($network_config)) $network_config = false;//$network_config_default;
	return $network_config;
}

function has_internet(){
    $connected = @fsockopen("cloud.precisionplanting.com",443); 
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
    }
    else{
        $is_conn = false; //action in connection failure
    }
    return $is_conn;
}

function get_wifi_networks(){
	$response = array(
		'response'=>'error',
		'error'=>'General catch',
		'networks'=>array(),
		'current_wifi_network'=>''
	);
	// Mac
	if (file_exists('/Applications/MAMP/')) {
	
		$rsp = shell_exec("/System/Library/PrivateFrameworks/Apple80211.framework/Versions/Current/Resources/airport -I | awk '/ SSID/ {print substr($0, index($0, $2))}'");
		$response['current_wifi_network'] = trim($rsp);
	
		$cmd = "/System/Library/PrivateFrameworks/Apple80211.framework/Versions/Current/Resources/airport -s"; // Mac
		$rsp = shell_exec($cmd);
		$response['raw'] = $rsp;
		$blocks = array(
			"SSID"=>"                            SSID",
			"BSSID"=>"BSSID             ",
			"RSSI"=>"RSSI ",
			"CHANNEL"=>"CHANNEL ",
			"HT"=>"HT ",
			"CC"=>"CC ",
			"SECURITY"=>"SECURITY (auth/unicast/group)                "
		);
		$lines = explode("\n",$rsp);
		unset($lines[0]);
		foreach ($lines as $line) {
			$network = array();
			$c = 0;
			foreach ($blocks as $name=>$block){
				$value = substr($line,$c,strlen($block));
				$c = ($c + strlen($block));
				$network[ $name ] = trim($value);
			}
			array_push($response['networks'],$network);
		}
		$response['response'] = 'success';
	}
	else { // Pi
		$cmd = "sudo iwlist wlan0 scan | grep ESSID";
		$rsp = shell_exec($cmd);
		$ESSIDs = explode("\n",trim($rsp));
		$networks = array();
		foreach ($ESSIDs as $line){
			list($junk,$ESSID) = explode("ESSID:",$line);
			$ESSID = trim(str_replace("\"","",$ESSID));
			array_push($networks,array(
				'SSID'=>$ESSID
			));
		}
		$response['networks'] = $networks;
		$response['response'] = 'success';
	}
	if ($response['response'] == 'success') unset($response['error']);
	return $response;
}

function read_wifi_networks() {
	$response = array(
		'response'=>'error',
		'error'=>'General catch',
		'networks'=>array(),
		'current_wifi_network'=>''
	);
	$network_config = get_networkConfig();
	$networks = array();
	foreach ($network_config['networks'] as $ESSID) {
		array_push($networks,array(
			'SSID'=>$ESSID
		));
	}
	$response['networks'] = $networks;
	$response['current_wifi_network'] = $network_config['current_wifi_network'];
	$response['response'] = 'success';
	if ($response['response'] == 'success') unset($response['error']);
	return $response;
}

function set_wifi_network($network='',$pass=''){
	$response = array(
		'response'=>'error',
		'error'=>'General catch'
	);
	
	$network = trim($network);
	$pass    = trim($pass);
	if ($network == '') {
		$response['error'] = 'Missing required param: network';
	}
	//elseif ($pass == '') { // pass may not be required
	//	$response['error'] = 'Missing required param: pass';
	//}
	else {
		// Mac
		//	networksetup -setairportnetwork en0 @re:Invent17 @aws2017
		if (file_exists('/Applications/MAMP/')) {
		
			// for Mac:
			// sudo vi /etc/group
			// add > admin:*:80:root,csm
		
			$cmd = trim("/usr/sbin/networksetup -setairportnetwork en0 '".$network."'".($pass ? " '".addslashes($pass)."'" : "")); // pass may not be required
			$response['cmd'] = $cmd;
			$response['whoami'] = shell_exec('whoami');
			shell_exec($cmd);
			$response['response'] = 'success';
			// expect silient attempt, takes too long for ASYNC response
		}
		else { // Pi

			$data  = "network={\n";
			$data .= "	ssid=\"".$network."\"\n";
			$data .= "	psk=\"".$pass."\"\n";
			$data .= "}\n";
			$filename = "/etc/wpa_supplicant.conf";
			$fp = fopen($filename,"w");
			fwrite($fp,$data);
			fclose($fp);
			
		}
	}
	if ($response['response'] == 'success') unset($response['error']);
	return $response;
}

function get_playlist(){
	global $playlist_file;
	if (file_exists($playlist_file)) {
		$data = shell_exec('cat "'.$playlist_file.'"');
		$data = trim($data);
		$data = @json_decode($data,true);
		if (!is_array($data)) $data = array();
	}
	else {
		$data = array();
	}
	return $data;
}

function get_kioskRemoteSource($fetchurl='',$savepathfile=false){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $fetchurl);
	if ($savepathfile) {
		if (file_exists($savepathfile)) unlink($savepathfile);
		$fp = fopen($savepathfile,'w');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}
	else {
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$result = curl_exec($ch); 
		curl_close($ch);
		return $result;
	}
}

function queue_set_wifi_network($network='',$pass='') {
	global $connect_file, $tstamp;
	$connect = array(
		'network'=>$network,
		'password'=>$pass
	);
	$fp = fopen($connect_file,"w");
	fwrite($fp,json_encode($connect));
	fclose($fp);
	chmod($connect_file,0777);
}

function set_networkMonitor(){
	global $network_file, $tstamp;
	$network_config = get_networkConfig();
	if ($network_config) {
		$network_config['monitoring'] = true;
		$network_config['last_monitor'] = $tstamp;
		$fp = fopen($network_file,"w");
		fwrite($fp,json_encode($network_config));
		fclose($fp);
	}
}