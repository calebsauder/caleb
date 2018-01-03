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
        $(".available-video-wrapper").draggable({
            scroll: false,
            helper: "clone",
            revert: true,
            zIndex: 100,
            connectToSortable: "#kiosk-roll-video-list",
            handle:'.sort-handle'
        });
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

        $(".kiosk-roll-video-wrapper").mouseover(function(){
            $(this).children(".functions-wrapper").fadeIn(300);
        }).mouseleave(function(){
            $(this).children(".functions-wrapper").fadeOut(300);
        });

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
                    _html += '<div class="sort-handle"><img class="icon" src="img/sort-black.png"></div>';
                    _html += '<select class="hotkey-select">';
                    	var hotkeys = [ // treat as strings!
                    		'',
                    		'1',
                    		'2',
                    		'3',
                    		'4',
                    		'5',
                    		'6',
                    		'7',
                    		'8',
                    		'9',
                    		'0',
                    	];
                    	for (var i = 0; i < hotkeys.length; i++) {
                    		_html += '<option value="'+hotkeys[i]+'"'+((_jsonVideoList[x].hotkey == hotkeys[i]) ? ' selected="selected"' : '')+'>'+((hotkeys[i] != '') ? hotkeys[i] : '--')+'</option>';
						}
                    _html += '</select>';
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
			hotkey:e.find('select.hotkey-select').val(),
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