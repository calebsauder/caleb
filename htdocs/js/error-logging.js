(function () {

	/* Allow LocalStorage to store objects and arrays.
		 From https://stackoverflow.com/a/10109307/863470
	 */
	Storage.prototype.setObj = function(key, obj) {
		return this.setItem(key, JSON.stringify(obj));
	};
	Storage.prototype.getObj = function(key) {
		return JSON.parse(this.getItem(key));
	};
	Storage.prototype.addItemToArr = function (key, item) {
		const arr = localStorage.getObj(key) || [];
		item.when = Date();
		arr.push(item);
		localStorage.setObj(key, arr);
	};

	// Javascript error logging
	window.onerror = function () {

		localStorage.addItemToArray('errors', {
			arguments: arguments,
			stack: arguments[4].stack
		});
		location.reload();

	};

	// Press L to save a snapshot
	$(window).keydown(function (event) {

		const which = event.which;

		// "E" triggers a test of the error logging functionality
		if (which == 69)
			doesntExist();

		// "L" saves a logging snapshot of the current page state
		if (which == 76) {

			const player = $('#player')[0];
			const mediaError = player.mediaError;
			const snapshot = {
				bodyHTML: $('body').html(),
				mediaError: mediaError ? '[' + mediaError.code + '] ' + mediaError.message : mediaError
			};

			['currentSrc', 'currentTime', 'networkState', 'paused', 'readyState'].forEach(function (prop) {
				snapshot[prop] = player[prop];
			});

			localStorage.addItemToArr('snapshots', snapshot);
			console.log(snapshot);

		}

	});

})();