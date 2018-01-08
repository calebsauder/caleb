Object.defineProperty(Array.prototype, "prepend", {
    enumerable: false,
    configurable: false,
    writable: false,
    value: function(e) {
        this.unshift(e);
        return this;
    }
});

let Kiosk = (function(){

    let availableVideos = [];
    
    let rollVideos = [];
        
	let deleteFromPlaylist = function(_rollVideoId) {
		for(let x in Kiosk.rollVideos){
			if(Kiosk.rollVideos[x].id == _rollVideoId){
				Kiosk.rollVideos.splice(x, 1);
			}
		}
		for(let x in Kiosk.availableVideos){
			for(let y in Kiosk.availableVideos[x].videos){
				if(Kiosk.availableVideos[x].videos[y].id == _rollVideoId){
					Kiosk.availableVideos[x].videos[y].selected = false;
				}
			}
		}
		loadRollVideos(Kiosk.rollVideos);
		loadVideoList(Kiosk.availableVideos);
		_createEvents();
	};

    let _createEvents = function(){
    
    	// Sort order
    	$('.sort-handles .icon.sort-up').click(function(){
    		const _rollVideoId = $(this).closest('.kiosk-roll-video-wrapper').data('id');
    		var lastx = false;
    		for (let x in Kiosk.rollVideos){
				if (Kiosk.rollVideos[x].id == _rollVideoId){
					var item = Kiosk.rollVideos[x];
					Kiosk.rollVideos.splice(x,1);
					Kiosk.rollVideos.splice(lastx,0,item);
					break;
				}
				lastx = x;
			}
			loadRollVideos(Kiosk.rollVideos);
    	});

		$('.sort-handles .icon.sort-down').click(function(){
    		const _rollVideoId = $(this).closest('.kiosk-roll-video-wrapper').data('id');
    		var itemx = false;
    		var item = false;
    		for (let x in Kiosk.rollVideos){
				if (item){
					Kiosk.rollVideos.splice(itemx,1);
					Kiosk.rollVideos.splice(x,0,item);
					break;
				}
				if (Kiosk.rollVideos[x].id == _rollVideoId){
					itemx = x;
					item = Kiosk.rollVideos[x];
				}
				lastx = x;
			}
			loadRollVideos(Kiosk.rollVideos);
    	});

        $(".available-video-wrapper").click(function(){
            let _videoId = $(this).attr("data-id");
            let _thisVideoIsSelected = $(this).attr("data-selected");
            if (_thisVideoIsSelected == "false"){
                $(".available-video-wrapper[data-id=" + _videoId + "]").attr("data-selected", true);
                for(let x in Kiosk.availableVideos){
                    for(let y in Kiosk.availableVideos[x].videos){
                        if(Kiosk.availableVideos[x].videos[y].id == $(this).attr("data-id")) {
                        	Kiosk.availableVideos[x].videos[y].selected = true;
						}
                    }
                }
                Kiosk.rollVideos.prepend({
                   "thumb": $(this).children("img").attr("src"),
                   "title": $(this).children(".video-title")[0].innerText,
                   "id": $(this).attr("data-id"),
                   "hotkey":""
                });
                loadRollVideos(Kiosk.rollVideos);
            }
        });
        /*
        $(".available-video-wrapper").draggable({
            scroll: false,
            helper: "clone",
            revert: true,
            zIndex: 100,
            connectToSortable: "#kiosk-roll-video-list",
            handle:'.sort-handle'
        });
        */
        /*
        $("#kiosk-roll-video-list").sortable({
            handle: ".sort-handle",
            axis: "y",
            placeholder: "aplaceholder",
            update: function (event, ui) {
                if($(ui.item).hasClass("kiosk-roll-video-wrapper")){
                    return;
                }
                let _droppedVideoId = ui.item[0].dataset.id;
                let _allowedToDrop = true;

                for(let x in Kiosk.availableVideos){
                    for(let y in Kiosk.availableVideos[x].videos){
                        if(_droppedVideoId == Kiosk.availableVideos[x].videos[y].id){
                            if(Kiosk.availableVideos[x].videos[y].selected){
                                _allowedToDrop = false;
                            }
                        }
                    }
                }
                if(_allowedToDrop){
                    for(let x in Kiosk.availableVideos){
                        for(let y in Kiosk.availableVideos[x].videos){
                            if(_droppedVideoId == Kiosk.availableVideos[x].videos[y].id){
                                Kiosk.availableVideos[x].videos[y].selected = true;
                            }
                        }
                    }
                    $(".available-video-wrapper[data-id=" + _droppedVideoId + "]").attr("data-selected", true);
                    rollVideos.splice(ui.item.index(), 0, {
                        "thumb": $(ui.item).children("img").attr("src"),
                        "title": $(ui.item).children("h3")[0].innerText,
                        "id": $(ui.item).attr("data-id"),
                    });
                    $(ui.item).remove();
                    loadRollVideos(rollVideos);
                }
                else{
                    $(ui.item).remove();
                }
            },
            stop: function(event, ui){
                $(ui.item).removeAttr("style");
            }
        });
        */

		/*
        $(".kiosk-roll-video-wrapper").mouseover(function(){
            $(this).children(".functions-wrapper").fadeIn(300);
        }).mouseleave(function(){
            $(this).children(".functions-wrapper").fadeOut(300);
        });
        */

        $(".kiosk-roll-video-wrapper .delete-button").click(function(){
            _rollVideoId = $(this).parents(".kiosk-roll-video-wrapper").attr("data-id");
            deleteFromPlaylist(_rollVideoId);
        })
    }

    let loadRollVideos = function(_jsonVideoList){
    	//console.log(_jsonVideoList);
        let _html = "";
        for(let x in _jsonVideoList){
            _html += '<div class="kiosk-roll-video-wrapper" data-id="' +_jsonVideoList[x].id + '">';
                _html += '<div class="functions-wrapper">'
                    //_html += '<div class="sort-handle"><img class="icon" src="img/sort-black.png"></div>';
                    _html += '<div class="sort-handles"><img class="icon sort-up" src="img/sort-up.png"><img class="icon sort-down" src="img/sort-down.png"></div>';
                    
					_html += '<div class="hotKeySelect">';
					_html += '	<input type="hidden" class="hotKeyInput" value="'+((_jsonVideoList[x].hotkey !== '') ? _jsonVideoList[x].hotkey : '')+'" autocomplete="off">';
					_html += '	<button type="button" onclick="openVideoHotKey(this)">Hot Key: <span class="videoHotKeyBtn">'+((_jsonVideoList[x].hotkey !== '') ? _jsonVideoList[x].hotkey : '--')+'</span></button>';
					_html += '	<ul>';
					_html += '		<li data-hotkey="" '+((_jsonVideoList[x].hotkey === '') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">&nbsp;</li>';
					_html += '		<li data-hotkey="0" '+((_jsonVideoList[x].hotkey === '0') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">0</li>';
					_html += '		<li data-hotkey="1" '+((_jsonVideoList[x].hotkey === '1') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">1</li>';
					_html += '		<li data-hotkey="2" '+((_jsonVideoList[x].hotkey === '2') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">2</li>';
					_html += '		<li data-hotkey="3" '+((_jsonVideoList[x].hotkey === '3') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">3</li>';
					_html += '		<li data-hotkey="4" '+((_jsonVideoList[x].hotkey === '4') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">4</li>';
					_html += '		<li data-hotkey="5" '+((_jsonVideoList[x].hotkey === '5') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">5</li>';
					_html += '		<li data-hotkey="6" '+((_jsonVideoList[x].hotkey === '6') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">6</li>';
					_html += '		<li data-hotkey="7" '+((_jsonVideoList[x].hotkey === '7') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">7</li>';
					_html += '		<li data-hotkey="8" '+((_jsonVideoList[x].hotkey === '8') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">8</li>';
					_html += '		<li data-hotkey="9" '+((_jsonVideoList[x].hotkey === '9') ? ' class="_selected"' : '')+' onclick="selectVideoHotKey(this)">9</li>';
					_html += '		<li data-hotkey="X" '+((_jsonVideoList[x].hotkey === '0') ? ' class="_selected"' : '')+' onclick="closeVideoHotKeySelect(this)">Close</li>';
					_html += '	</ul>';
					_html += '	<div class="_clear"></div>';
					_html += '</div>';
                    
                    _html += '<button class="delete-button"><img class="icon" src="img/delete-black.png"></button>';
                _html += '</div>';
                _html += '<div class="not-downloaded"><img class="icon" src="img/warning.png"></div>';
                //_html += '<video preload="metadata" class="kiosk-roll-video" type="video/mp4" src="' + _jsonVideoList[x].url + '"></video>'
                _html += '<img class="kiosk-roll-video" src="' + _jsonVideoList[x].thumb + '">'
                _html += '<h3 class="kiosk-roll-video-title">' + _jsonVideoList[x].title + '</h3>';
            _html += "</div>";
        }

        $("#kiosk-roll-video-list").html(_html);

        _createEvents();
    }

    let loadVideoList = function(_jsonVideoList){
        let _html = "";
        for(let x in _jsonVideoList){
            _html += '<h2 class="video-category-title">' + _jsonVideoList[x].category + '</h2>'
            _html += '<div class="video-list">';
            for(let y in _jsonVideoList[x].videos){
                _html += '<div class="available-video-wrapper" data-selected="' + _jsonVideoList[x].videos[y].selected + '" data-id="' + _jsonVideoList[x].videos[y].id + '">';
                _html += '<div class="in-roll" onclick="Kiosk.deleteFromPlaylist(\''+_jsonVideoList[x].videos[y].id+'\')"><img class="icon" src="img/check-black.png"></div>';
            //_html += '<video class="available-video" preload="metadata" type="video/mp4" src="' + _jsonVideoList[x].videos[y].url + '"></video>';
                    _html += '<img class="available-video" src="' + _jsonVideoList[x].videos[y].thumb + '">';
                    _html += '<h3 class="video-title">' + _jsonVideoList[x].videos[y].title + '</h3>'
               _html += '</div>';
            }
            _html += '<div class="clear-float"></div>';
            _html += '</div>';
        }

        $("#video-list-wrapper").html(_html);

        _createEvents();
    }

    return{
        availableVideos: availableVideos,
        loadRollVideos: loadRollVideos,
        loadVideoList: loadVideoList,
        rollVideos: rollVideos,
        deleteFromPlaylist:deleteFromPlaylist
    }

})();

function init_playlistConfig(){
	$.ajax({
		type: "POST",
		url: 'ajax/kiosk-controller.php',
		data: {
			action:'get-playlist'
		},
		success: function(prsp){
			var vids = [];
			var saveVids = {};
			for (var i = 0; i < prsp.data.length; i++) {
				vids.push(prsp.data[i].id);
				saveVids[ prsp.data[i].id ] = prsp.data[i];
			}
			//console.log(saveVids);
			$.ajax({
				type: "GET",
				url: 'https://cloud.precisionplanting.com/kiosk/get/',
				data: {},
				success: function(rsp){
				
					var rollVids = {};
					for (var i = 0; i < rsp.data.length; i++) {
						for (var vi = 0; vi < rsp.data[i].videos.length; vi++) {
							var this_vid = parseInt(rsp.data[i].videos[vi].id);
							if (vids.indexOf(this_vid) > -1) {
								rsp.data[i].videos[vi].selected = true;
								if (!rollVids[ this_vid ]) {
									rollVids[ this_vid ] = {
									   "thumb": rsp.data[i].videos[vi].thumb,
									   "title": rsp.data[i].videos[vi].title,
									   "id": this_vid,
									   "hotkey": saveVids[ this_vid ].hotkey
									};
								}
							}
						}
					}
					
					var rollVidsSorted = [];
					for (var i = 0; i < vids.length; i++) {
						if (rollVids[ vids[i] ]) rollVidsSorted.push(rollVids[ vids[i] ]);
					}
				
					Kiosk.availableVideos = rsp.data;
					Kiosk.rollVideos = rollVidsSorted;
					
					Kiosk.loadVideoList(Kiosk.availableVideos);
					Kiosk.loadRollVideos(Kiosk.rollVideos);
				}
			});	
		}
	});
};

function savePlaylist(){
	var playlist = [];
	$('#kiosk-roll-video-list .kiosk-roll-video-wrapper').each(function(){
		var e = $(this);
		var video = {
			id:e.data('id'),
			//hotkey:e.find('select.hotkey-select').val(),
			hotkey:e.find('input.hotKeyInput').val(),
			title:e.find('.kiosk-roll-video-title').html()
		};
		playlist.push(video);
	});
	var d = $('#dimwin');
		d.addClass('_show');
	$.ajax({
		type: "POST",
		url: 'ajax/kiosk-controller.php',
		data: {
			action:'save-playlist',
			data:JSON.stringify(playlist)
		},
		success: function(rsp){
			$('#save-rsp').val(JSON.stringify(rsp));
			d.removeClass('_show');
			swal("Sync Complete","Video resources successfully downloaded!","success");
		}
	});
}

function selectVideoHotKey(e){
	var e = $(e);
	var hotKey = e.data('hotkey');
	var p = e.closest('.hotKeySelect');
	
	$('.hotKeySelect').each(function(){
		var hk = $(this);
		var v = hk.find('input.hotKeyInput').val();
		if (v == hotKey) {
			hk.find('input.hotKeyInput').val('');
			hk.find('.videoHotKeyBtn').html('--');
		}
	});
	
	p.find('input.hotKeyInput').val( hotKey );
	p.find('.videoHotKeyBtn').html( (hotKey !== '') ? hotKey : '--' );
	var lis = p.find('li');
	lis.each(function(){
		var li = $(this);
		if (li.data('hotkey') === hotKey) {
			li.addClass('_selected');
		}
		else {
			li.removeClass('_selected');
		}
	});
	
	// Save the hotkey to the Kiosk object
	var videoID = p.closest('.kiosk-roll-video-wrapper').data('id');
	$.each(Kiosk.rollVideos, function (i, video) {
		if (video.id == videoID) {
			video.hotkey = hotKey;
			return false;
		}
	});
	
	closeVideoHotKeySelect(e);
	
}
function openVideoHotKey(e){
	$('.hotKeySelect ul._show').each(function(){
		$(this).removeClass('_show');
	});
	var e = $(e);
	var p = e.closest('.hotKeySelect');
	var ul = p.find('ul');
	ul.addClass('_show');
}
function closeVideoHotKeySelect(e){
	var e = $(e);
	var ul = e.closest('ul');
	ul.removeClass('_show');
}