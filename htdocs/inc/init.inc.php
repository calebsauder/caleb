<?php

require_once('kiosk-functions.inc.php');

$base = '/usr/share/apache2/htdocs/';
if (strstr($_SERVER['DOCUMENT_ROOT'],'MAMP')) $base = '/Applications/MAMP/htdocs/KIOSK/htdocs/';

$data_file = $base.'playlist.json';
$video_dir = $base.'videos/';
$video_url = 'videos/';