<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields\Options;

use const \Rodzeta\Feedbackfields\CONFIG;

function Update($data) {
	echo "<pre>"; var_dump(CONFIG, $data["fields"]); echo "</pre>";

	//\Encoding\PhpArray\Write(FILE_OPTIONS . "/options.php", $options);
}

function Select() {
	$fname = CONFIG . "/options.php";
	return is_readable($fname)? include $fname : [];
}
