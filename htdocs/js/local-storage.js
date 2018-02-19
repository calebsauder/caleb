(function () {

	function printLocalStorage () {
		$('#ls-json').text(JSON.stringify(localStorage));
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