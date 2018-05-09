<?php

require_once("inc/init.inc.php");

if (has_internet()) {
	header("Location:playlist.php");
}
else {
	header("Location:network.php");
}
exit;

?>