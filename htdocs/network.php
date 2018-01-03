<?php
 
//echo shell_exec('whoami');
//exit;

require_once("inc/init.inc.php");
$data_page = '';
include("inc/head.inc.php");

set_networkMonitor();

?>
<header id="video-wrapper-header">
	<a href="index.php" id="video-wrapper-back-button" class="video-header-button">
		<img class="icon pad-right" src="img/chevron-left.png">Back
	</a>
	<h1 id="video-wrapper-header-title"><img id="video-list-pp-logo" src="img/logo.png">Network</h1>
	<a href="playlist.php" id="video-wrapper-save-button" class="video-header-button">
		<img class="icon pad-right" src="img/gear.png">Configure Playlist
	</a>
	<div class="clear-float"></div>
</header>
<section id="setup-screen-wrapper" style="padding-top:12%">
	<div id="setup-screen-error-wrapper">
		<p class="warning-text"><img class="icon pad-right" src="img/warning.png">This kiosk is not connected to the internet. Please plug it into your local network using the ethernet port on this Kiosk. You only need to do this to load your initial video playlist.</p>
	</div>
	<div id="setup-screen-success-wrapper">
		<p class="success-text"><img class="icon pad-right" src="img/check.png">This kiosk is connected to the internet. <span id="current_wifi_network"></span></p>
	</div>
	<div class="clear"></div>
	
	<select type="select" name="selectTest" style="width:200px" onclick="alert('hi')">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
	</select>
	
</section>

<?php

$ondomready = ("
	init_network();
");

include("inc/foot.inc.php");

?>