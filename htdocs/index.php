<?php

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
			<span class="welcome-screen-button-icon">&#9881;</span>Set-up
		</a>
		<a href="player.php" id="welcome-screen-start-button" class="welcome-screen-button">
			<span class="welcome-screen-button-icon">&#8227;</span>Start Kiosk
		</a>
		<br>
		<?php
		echo "WHOAMI: [".shell_exec('whoami')."]<br>";
		echo "WCLIST: [".shell_exec('sudo wclist scan | grep ESSID')."]<br>";
		?>
	</div>
</section>
<?php

include("inc/foot.inc.php");

?>