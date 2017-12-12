<?php

require_once("../inc/init.inc.php");

$action = $_POST['action'];

$response = array(
	'response'=>'error',
	'error'=>'General catch'
);

if ($action == 'check-internet') {
	set_networkMonitor();
	$internet = has_internet();
	if ($internet) {
		$response['response'] = 'success';
	}
	else {
		$response['error'] = "No intenet connection detected";
	}
}

if ($action == 'get-networks') {
	$rsp = read_wifi_networks();
	if ($rsp['response'] == 'success') {
		$response['response'] = 'success';
		$names = array();
		foreach ($rsp['networks'] as $network){
			if ($network['SSID'] != '') array_push($names,$network['SSID']);
		}
		$names = array_unique($names);
		sort($names);
		$response['networks'] = $names;
		$response['current_wifi_network'] = $rsp['current_wifi_network'];
	}
	else {
		$response['error'] = $rsp['error'];
	}
}

if ($action == 'set-wifi-network') {
	$rsp = set_wifi_network($_POST['network'],$_POST['pass']);
	$response = $rsp; // NOT ASYNC
}

if ($action == 'save-playlist') {
	$data = $_POST['data'];
	$fp = fopen($playlist_file,"w");
	fwrite($fp,$data);
	fclose($fp);
	
	// start sync
	$downloads = array();
	$jsdata = @json_decode($data,true);
	if (!is_array($jsdata)) $jsdata = array();
	foreach ($jsdata as $video){
		$id = $video['id'];
		$filename = $id.".mp4";
		$fetchurl = "https://cloud.precisionplanting.com/kiosk/video/".$id;
		$checksumurl = "https://cloud.precisionplanting.com/kiosk/checksum/".$id;
		$checksum = get_kioskRemoteSource($checksumurl);
		$download = array(
			'response'=>'error',
			'error'=>'General catch',
			'video_id'=>$id,
			'filename'=>$filename,
			'checksum'=>$checksum
		);
		$download_file = true;
		if (file_exists($video_dir.$filename)) {
			if (md5($video_dir.$filename) == $checksum) $download_file = false;
		}
		if ($download_file) {
			get_kioskRemoteSource($fetchurl,$video_dir.$filename);
			if (file_exists($video_dir.$filename)) {
				$download['checksum'] = md5($video_dir.$filename);
				$download['filesize'] = filesize($video_dir.$filename);
				$download['response'] = 'success';
				$download['status'] = 'downloaded';
				unset($download['error']);
			}
			else {
				$download['error'] = "Error downloading video from server.";
			}
		}
		else {
			$download['status'] = 'no change';
			$download['response'] = 'success';
			unset($download['error']);
		}
		array_push($downloads,$download);
	}
	$response['downloads'] = $downloads;
	
	$response['response'] = 'success';
}

if ($action == 'get-playlist') {
	$response['response'] = 'success';
	$response['data'] = get_playlist();
}

if ($action == 'set-network-monitor') {
	set_networkMonitor();
	$response['response'] = 'success';
}

if ($response['response'] == 'success') unset($response['error']);

header('Content-type: application/json');
echo json_encode($response);
exit;
