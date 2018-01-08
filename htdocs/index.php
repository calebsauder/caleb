<img src="resin-logo.png">

<?php

phpinfo();
exit;

require_once("inc/init.inc.php");
$body_cls = 'fullscreen';
$data_page = 'welcome';
include("inc/head.inc.php");

?>
<section id="welcome-screen-wrapper">
	<h1 id="welcome-screen-title">Welcome</h1>
	<img id="main-screens-pp-logo" src="img/logo.png">
	<div id="welcome-screen-button-wrapper">
		<a href="check-network.php" id="welcome-screen-setup-button" class="welcome-screen-button">
			<span class="welcome-screen-button-icon"><img class="icon pad-right" src="img/gear.png"></span>Set-up
		</a>
		<a href="player.php" id="welcome-screen-start-button" class="welcome-screen-button">
			<span class="welcome-screen-button-icon"><img class="icon pad-right" src="img/play.png"></span>Start Kiosk
		</a>
	</div>
</section>

<?php

$ondomready .= ("
	initAutoPlay();
");

include("inc/foot.inc.php");

?>