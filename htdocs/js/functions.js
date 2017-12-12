function rn() {
	return Math.floor(Math.random()*999999999);
}
 
function init_network(){
	var checkInternetRunning = false;
	var checkInternet = function(){
		if (!checkInternetRunning) {
			checkInternetRunning = 1;
			$.ajax({
				type: "POST",
				url: 'ajax/kiosk-controller.php?rn='+rn(),
				data: {
					action:'check-internet'
				},
				success: function (rsp) {
					checkInternetRunning = false;
					if (rsp['response'] == 'success') {
						$('#setup-screen-error-wrapper').removeClass('_show');
						$('#setup-screen-success-wrapper').addClass('_show');
						$('#video-wrapper-save-button').show();
					}
					else {
						$('#setup-screen-error-wrapper').addClass('_show');
						$('#setup-screen-success-wrapper').removeClass('_show');
						$('#video-wrapper-save-button').hide();
					}
				}
			});
		}
		else {
			checkInternetRunning++;
			if (checkInternetRunning > 3) checkInternetRunning = false;
		}
	};
	setInterval(checkInternet,2000);
	checkInternet();
	
	var lastNetworkList = '';
	var getNetworksRunning = false;
	var getNetworks = function(){
		if (!getNetworksRunning) {
			getNetworksRunning = 1;
			$.ajax({
				type: "POST",
				url: 'ajax/kiosk-controller.php?rn='+rn(),
				data: {
					action:'get-networks'
				},
				success: function (rsp) {
					getNetworksRunning = false;
					var s = $('#wifi-setup-select');
					var v = s.val();
					if (rsp['response'] == 'success') {
						if (rsp.current_wifi_network != '') {
							$('#current_wifi_network').html('Current network: <strong>'+rsp.current_wifi_network+'</strong>');
						}
						else {
							$('#current_wifi_network').html('');
						}
						if (v == '') v = rsp.current_wifi_network;
						var snap = JSON.stringify(rsp.networks);
						if (snap != lastNetworkList) {
							//console.log(lastNetworkList);
							lastNetworkList = snap;
							if (rsp.networks.length) {
								var os = '<option value="">--</option>';
								for (var i = 0; i < rsp.networks.length; i++) os += '<option value="'+rsp.networks[i]+'"'+((v == rsp.networks[i]) ? ' selected="selected"' : '')+'>'+rsp.networks[i]+'</option>';
								s.html(os);
								$('#looking-spinner').addClass('_hide');
							}
							else {
								$('#looking-spinner').removeClass('_hide');
								s.html('<option value="">Looking for networks...</option>');
							}
						}
					}
					else {
						s.html('<option value="">Looking for networks...</option>');
						$('#looking-spinner').removeClass('_hide');
					}
				}
			});
		}
		else {
			getNetworksRunning++;
			if (getNetworksRunning > 2) getNetworksRunning = false;
		}
	};
	setInterval(getNetworks,5000);
	getNetworks();
}

var spinnerTimer = false;
function connectToNetwork(){
	var params = {
		action:'set-wifi-network',
		network:$('#wifi-setup-select').val(),
		pass:$('#wifi-setup-input').val()
	};
	if (params.network == '') {
		swal('Oops!','Please select a WiFi network to connect to.','error');
		return false;
	}
	else {
		if (spinnerTimer) clearTimeout(spinnerTimer);
		var spinner = $('#connect-spinner');
		spinner.addClass('_show');
		spinnerTimer = setTimeout(function(){
			spinner.removeClass('_show');
		},8000);
		$.ajax({
			type: "POST",
			url: 'ajax/kiosk-controller.php?rn='+rn(),
			data: params,
			success: function (rsp) {
				//
			}
		});
	}
}

function init_player(){
	var playlist = [];
	$('#playlist li').each(function(){
		var p = $(this);
		playlist.push({
			id:p.data('id'),
			video:p.data('video'),
			hotkey:p.data('hotkey'),
			title:p.html()
		});
	});
	console.log(playlist);
	var player = $('#player');
	var playVideo = function(index){
		if (index == 'next') {
			index = parseInt(player.data('index'));
			if (isNaN(index)) index = 0;
			index++;
		}
		if (index == 'previous') {
			index = parseInt(player.data('index'));
			if (isNaN(index)) index = 0;
			index--;
			if (index < 0) index = (playlist.length - 1);
		}
		if (index > (playlist.length - 1)) index = 0;
		var video = playlist[index];
		$('#video-title').html(video.title);
		//player.html('<source src="'+video.video+'" type="video/mp4">Your browser does not support the video tag.');
		player.prop('src', video.video);
		player.data('index',index);
		player[0].play();
		console.log(video);
	};
	player[0].addEventListener('ended',function(){
		console.log('ended');
		playVideo('next');
	},false);
	var playHotKeyVideo = function(hotkey){
		var gotit = false;
		for (var i = 0; i < playlist.length; i++) {
			if ((!gotit) && (playlist[i].hotkey == hotkey+'')) {
				gotit = true;
				playVideo(i);
			}
		}
		if (!gotit) console.log('Hot key ['+hotkey+'] unassigned');
	};
	$(window).on("keydown",function(event){
		var keycode = event.which;
		if (keycode == 37) { // left arrow
			playVideo('previous');
		}
		else if (keycode == 39) { // right arrow
			playVideo('next');
		}
		else if (keycode == 32) { // space bar
			playVideo('next');
		}
		else if (keycode == 13) { // enter
			playVideo('next');
		}
		else if (keycode == 27) { // esc
			window.location = 'index.php';
		}
		else if (keycode == 48) { // 0
			playHotKeyVideo(0);
		}
		else if (keycode == 49) { // 1
			playHotKeyVideo(1);
		}
		else if (keycode == 50) { // 2
			playHotKeyVideo(2);
		}
		else if (keycode == 51) { // 3
			playHotKeyVideo(3);
		}
		else if (keycode == 52) { // 4
			playHotKeyVideo(4);
		}
		else if (keycode == 53) { // 5
			playHotKeyVideo(5);
		}
		else if (keycode == 54) { // 6
			playHotKeyVideo(6);
		}
		else if (keycode == 55) { // 7
			playHotKeyVideo(7);
		}
		else if (keycode == 56) { // 8
			playHotKeyVideo(8);
		}
		else if (keycode == 57) { // 9
			playHotKeyVideo(9);
		}
		else {
			
		}
		event.preventDefault();
		return false;
	});
	if (playlist.length) {
		$('#require-setup').removeClass('_show');
		playVideo(0);
	}
	else {
		$('#require-setup').addClass('_show');
	}
}