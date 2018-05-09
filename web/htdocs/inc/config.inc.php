<?php

	/**
	 * Each environment has the following configuration options:
	 * 		data_dir		string	required	The "data" root directory path, beginning from the file system root and ending with a "/".
	 * 		detect_dir	string	optional	A directory to check for to detect the presence of this environment. If not provided, then
	 * 																	this will be used as the default environment.
	 * 																	information will be shown.
	 * 		qa					boolean	optional	Whether this environment is a QA environment. If it is, then debugging information will be shown.
	 * 																	Defaults to FALSE (this is a production environment).
	 * 		root_url		string	required	The root URL to access the Kiosk app from in this environment, beginning and ending with a "/".
	 */
	define("ENVIRONMENTS", [

		// ROOT on pi:
		// /usr/share/apache2/htdocs/
		"RASPBERRY_PI" => [
			"data_dir" => "/data/",
			"root_url" => "/",
			"qa" => true
		],
		"MAMP" => [
			"detect_dir" => "/Applications/MAMP",
			"root_url" => "/",
			"data_dir" => "/Applications/MAMP/htdocs/data/",
			"qa" => true
		],
		"XAMPP" => [
			"detect_dir" => "/Applications/XAMPP/xamppfiles",
			"root_url" => "/pp-kiosk/",
			"data_dir" => "/Applications/XAMPP/xamppfiles/htdocs/pp-kiosk/data/",
			"qa" => true
		]
	]);

	$detected_env = null;
	foreach (ENVIRONMENTS as $env_name => $env_config) {
		$detect_dir = $env_config["detect_dir"];
		if ($detect_dir) {
			if (file_exists($detect_dir))
				$detected_env = $env_name;
		}
		else
			$default_env = $env_name;
	}

	define("ENV_NAME", $detected_env ? $detected_env : $default_env);
	define("ENV_CONFIG", ENVIRONMENTS[ENV_NAME]);
	define("ROOT_URL", ENV_CONFIG["root_url"]);
	define("QA", !!ENV_CONFIG["qa"]);

	// location below root of data directory
	$base = ENV_CONFIG["data_dir"];