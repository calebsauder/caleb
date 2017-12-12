<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
date_default_timezone_set('America/Chicago');
ini_set('display_errors', 1); 

require_once('kiosk-functions.inc.php');

$tstamp = mktime();

$base = '/data/'; // location below root of data directory
if (file_exists('/Applications/MAMP/')) $base = '/Applications/MAMP/htdocs/data/';
 
$cron_marker = $base.'cron-running.json';
$network_file = $base.'network.json';
$playlist_file = $base.'playlist.json';
$connect_file = $base.'connect.json';
$video_dir = $base.'videos/';
$video_url = 'video.php?v=';

$network_config_default = array(
	'monitoring'=>false,
	'last_monitor'=>0,
	'networks'=>array(),
	'current_wifi_network'=>''
);

// create video directory
if (!file_exists($video_dir)) mkdir($video_dir, 0777);

if (!file_exists($network_file)) {
	$fp = fopen($network_file,"w");
	fwrite($fp,json_encode($network_config_default));
	fclose($fp);
	chmod($network_file,0777);
}

if (!file_exists($playlist_file)) {
	$fp = fopen($playlist_file,"w");
	fwrite($fp,"[]");
	fclose($fp);
	chmod($playlist_file,0777);
}

