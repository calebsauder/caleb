<?php

require_once("../inc/init.inc.php");
 
$action = $_POST['action'];

$response = array(
	'response'=>'error',
	'error'=>'General catch'
);

if ($action == 'sync-status') {
	$status = '';
	$fp = fopen($sync_file,"r");
	$record = file($sync_file);
	for ($n=0; $n<count($record); $n++) $status .= $record[$n];
	$response['response'] = 'success';
	$response['status'] = trim($status);
}

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
	queue_set_wifi_network($_POST['network'],$_POST['pass']);
	$response['response'] = 'success'; // NOT ASYNC
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
	
	writeSync('Beginning sync...');
	
	$n = 1;
	foreach ($jsdata as $video){
		$t = 'Analyzing video '.$n.' of '.count($jsdata).'...';
		writeSync($t);
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
			'orig_checksum'=>$checksum,
			'this_checksum'=>md5($video_dir.$filename),
			'checksum'=>$checksum
		);
		writeSync($t.'...comparing checksum');
		$download_file = true;
		if (file_exists($video_dir.$filename)) {
			if (md5($video_dir.$filename) == $checksum) {
				$download_file = false;
				writeSync($t.'...currently downloaded video is latest version.');
			}
		}
		if ($download_file) {
			writeSync($t.'...downloading video.');
			get_kioskRemoteSource($fetchurl,$video_dir.$filename);
			if (file_exists($video_dir.$filename)) {
				$download['checksum'] = md5($video_dir.$filename);
				$download['filesize'] = filesize($video_dir.$filename);
				$download['response'] = 'success';
				$download['status'] = 'downloaded';
				unset($download['error']);
				writeSync($t.'...video downloaded.');
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
		$n++;
	}
	$response['downloads'] = $downloads;
	
	writeSync('Sync complete!');
	
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
