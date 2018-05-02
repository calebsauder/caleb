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
		$response['error'] = "No internet connection detected";
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

	$current_playlist_data = '';
	$fp = fopen($playlist_file,"r");
	$record = file($playlist_file);
	for ($n=0; $n<count($record); $n++) {
		$current_playlist_data .= $record[$n];
	}
	$current_playlist_data = @json_decode($current_playlist_data,true);
	if (!is_array($current_playlist_data)) $current_playlist_data = array();

	$data = $_POST['data'];
	$fp = fopen($playlist_file,"w");
	fwrite($fp,$data);
	fclose($fp);
	
	// start sync
	$downloads = array();
	$jsdata = @json_decode($data,true);
	if (!is_array($jsdata)) $jsdata = array();
	
	writeSync('Beginning sync...');
	
	$live_files = array();
	
	$n = 1;
	foreach ($jsdata as $video){
		$t = 'Syncing video '.$n.' of '.count($jsdata).'...';
		writeSync($t.'checking version.');
		$id = $video['id'];
		$filename = $id.".mp4";
		$fetchurl = "https://cloud.precisionplanting.com/kiosk/video/".$id;
		
		$local_last_updated = 0;
		foreach ($current_playlist_data as $cpd) {
			if ($cpd['id'] == $id) {
				if (isset($cpd['last_updated'])) $local_last_updated = (int)$cpd['last_updated'];
			}
		}
		
		$server_last_updated_url = "https://cloud.precisionplanting.com/kiosk/last_updated/".$id;
		$server_last_updated = get_kioskRemoteSource($server_last_updated_url);
		$server_last_updated = (int)$server_last_updated;
		
		$download = array(
			'response'=>'error',
			'error'=>'General catch',
			'video_id'=>$id,
			'filename'=>$filename,
			'server_last_updated'=>$server_last_updated,
			'local_last_updated'=>$local_last_updated
		);
		writeSync($t.'...comparing time stamps');
		if ($server_last_updated == $local_last_updated) {
			$download['status'] = 'cached';
			writeSync($t.'...currently downloaded video is latest version.');
			$download['response'] = 'success';
		}
		else {
			writeSync($t.'...downloading video.');
			get_kioskRemoteSource($fetchurl,$video_dir.$filename);
			if (file_exists($video_dir.$filename)) {
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
		
		if ($download['response'] == 'success') {
			unset($download['error']);
			array_push($live_files,$filename);

			// save last_updated into playlist
			foreach ($jsdata as $indx=>$jsd) {
				if ($jsd['id'] == $id)$jsdata[ $indx ]['last_updated'] = $server_last_updated;
			}
			$fp = fopen($playlist_file,"w");
			fwrite($fp,json_encode($jsdata));
			fclose($fp);
		}
				
		array_push($downloads,$download);
		$n++;
	}
	$response['downloads'] = $downloads;
	
	// garbage collection
	$deleted_files = array();
	$open = opendir($video_dir);
	while ($file = readdir($open)) {
		if (is_file($video_dir.$file)) {
			if (!in_array($file,$live_files)) {
				unlink($video_dir.$file);
				array_push($deleted_files,$video_dir.$file);
			}
		}
	}
	$response['deleted_files'] = $deleted_files;
	
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
