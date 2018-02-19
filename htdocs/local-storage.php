<?php

	require_once("inc/init.inc.php");
	include("inc/head.inc.php");

?>

<div style='color:white; font-family: sans-serif'>
	<h1>Local Storage Viewer</h1>
	<p>
		<button id='ls-set' type='button'>Set LocalStorage test variable</button>
		<button id='ls-clear' type='button'>Clear LocalStorage</button>
		<a href='' style='color: inherit'>Refresh Page</a>
		<a href='index.php' style='color: inherit'>Back to Home</a>
	</p>
	<div id='ls-json'></div>
</div>

<?php

	$add_to_foot .= "<script src='js/local-storage.js'></script>";
	include("inc/foot.inc.php");