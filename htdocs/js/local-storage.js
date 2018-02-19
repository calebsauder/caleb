(function () {

	function printLocalStorage () {
		$('#ls-json').text(JSON.stringify(localStorage));
	}

	$('#ls-set').click(function () {
		localStorage.testValue = 'This is the test value.';
		printLocalStorage();
	});

	$('#ls-clear').click(function () {
		if (confirm('Are you sure you want to clear all LocalStorage?')) {
			localStorage.clear();
			printLocalStorage();
		}
	});

	printLocalStorage();

})();