<?php

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
	if (strstr($_SERVER['DOCUMENT_ROOT'],'MAMP')) {
	
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
		$cmd = "iwlist wlan0 scan | grep ESSID";
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

function set_wifi_network($network='',$pass=''){
	$response = array(
		'response'=>'error',
		'error'=>'General catch'
	);
	
	$network = trim($network);
	$pass = trim($pass);
	if ($network == '') {
		$response['error'] = 'Missing required param: network';
	}
	//elseif ($pass == '') { // pass may not be required
	//	$response['error'] = 'Missing required param: pass';
	//}
	else {
		// Mac
		//	networksetup -setairportnetwork en0 @re:Invent17 @aws2017
		if (strstr($_SERVER['DOCUMENT_ROOT'],'MAMP')) {
		
			// for Mac:
			// sudo vi /etc/group
			// add > admin:*:80:root,csm
		
			$cmd = trim("networksetup -setairportnetwork en0 '".$network."'".($pass ? " '".addslashes($pass)."'" : "")); // pass may not be required
			$response['cmd'] = $cmd;
			$response['whoami'] = shell_exec('whoami');
			shell_exec($cmd);
			$response['response'] = 'success';
			// Failed to join network*
			// expect silient attempt, takes too lon for ASYNC response
		}
		else {
			/* Pi
			sudo nano /etc/wpa_supplicant/wpa_supplicant.conf

			Go to the bottom of the file and add the following:

			network={
				ssid="testing"
				psk="testingPassword"
			}
			*/	
		}
	}
	if ($response['response'] == 'success') unset($response['error']);
	return $response;
}

function get_playlist(){
	global $data_file;
	if (file_exists($data_file)) {
		$data = shell_exec('cat "'.$data_file.'"');
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

