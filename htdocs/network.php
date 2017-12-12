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
		<i class="fa fa-chevron-left" aria-hidden="true"></i> Back
	</a>
	<h1 id="video-wrapper-header-title"><img id="video-list-pp-logo" src="img/logo.png">Network</h1>
	<a href="playlist.php" id="video-wrapper-save-button" class="video-header-button">
		<i class="fa fa-cog" aria-hidden="true"></i> Configure Playlist
	</a>
	<div class="clear-float"></div>
</header>
<section id="setup-screen-wrapper">
	<div id="setup-screen-error-wrapper">
		<p class="warning-text"><span class="warning-icon">&#9888;</span>This kiosk is not connected to the internet. Please plug it into your local network or select a WiFi network.</p>
	</div>
	<div id="setup-screen-success-wrapper">
		<p class="success-text"><span class="success-icon">&check;</span>This kiosk is connected to the internet. <span id="current_wifi_network"></span></p>
	</div>
	
	<div class="clear"></div>
	
	<div id="setup-connection-wrapper">
		<h2 id="setup-wifi-title">Available WiFi Networks</h2>
		<form>
			<label class="wifi-setup-label">
				SSID <div id="looking-spinner" class="spinner"></div>
				<select type="select" id="wifi-setup-select" autocomplete="off"><option value="">Looking for networks...</option></select>
			</label>
			<br>
			<label class="wifi-setup-label">
				Pass
				<input id="wifi-setup-input" type="password" placeholder="Enter network password..." autocomplete="off" maxlength="150">
			</label>
		</form>
		<div class="connect-btn">
			<button id="wifi-connect-button" onclick="connectToNetwork()">Connect</button>
			<div id="connect-spinner" class="spinner"></div>
		</div>
	</div>
</section>

<?php

$ondomready = ("
	init_network();
");

include("inc/foot.inc.php");

?>