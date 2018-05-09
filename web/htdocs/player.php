<?php

require_once("inc/init.inc.php");

$body_cls = 'fullscreen player';
include("inc/head.inc.php");

$playlist = get_playlist();

?>

<video id="player" data-index="0"></video>
<ul id="playlist">
<?php

$n = 1;
foreach ($playlist as $video){
	if (file_exists($video_dir.$video['id'].'.mp4')) {
		echo '<li id="v'.$video['id'].'" data-id="'.$video['id'].'" data-hotkey="'.$video['hotkey'].'" data-video="'.$video_url.$video['id'].'.mp4"><span><img src="img/chevron-right.png"></span>'.$video['title'];
		if ($video['hotkey'] != '') echo '<div>Hot key: '.$video['hotkey'].'</div>';
		if ($n == count($playlist)) {
			echo '<div style="margin-top:20px; text-align:right">Esc to exit</div>';
		}
		echo '</li>';
	}
	$n++;
}

?>
</ul>
<div id="video-title"></div>
<div id="require-setup">This kiosk requires set-up. Please hit <em>ESC</em> to return to main menu.</div>
<?php

$ondomready = ("
	init_player();
");

$add_to_foot .= "<script src='js/error-logging.js'></script>";

include("inc/foot.inc.php");

?>