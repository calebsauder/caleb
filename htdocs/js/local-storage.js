(function () {

	const $lsJSON = $('#ls-json');

	/* Allow LocalStorage to store objects and arrays.
		 From https://stackoverflow.com/a/10109307/863470
	 */
	Storage.prototype.setObj = function(key, obj) {
		return this.setItem(key, JSON.stringify(obj));
	};
	Storage.prototype.getObj = function(key) {
		return JSON.parse(this.getItem(key));
	};

	function getObjArrEls (arr) {
		return $('<ol style="font-family: monospace">').append($.map(arr, function (obj) {
			return $('<li>').append($('<dl>').append($.map(obj, function (value, key) {
				return $('<dt>')
					.text(key)
					.add(
						$('<dd>').text(JSON.stringify(value))
					);
			})));
		}));
	}

	function printLocalStorage () {
		$lsJSON.empty();
		['errors', 'snapshots'].forEach(function (type) {
			$lsJSON.append('<h2>' + type + '</h2>', getObjArrEls(localStorage.getObj(type)));
		});
	}

	$('#ls-set').click(function () {
		localStorage.testValue = 'This is the test value.';
		printLocalStorage();
	});

	$('#ls-clear').click(function () {
		localStorage.clear();
		printLocalStorage();
	});

	printLocalStorage();

})();