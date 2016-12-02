<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields\Options;

use const \Rodzeta\Feedbackfields\CONFIG;

function Update($data) {
	$fields = [];
	foreach ($data["fields"] as $row) {
		$row = array_map("trim", $row);
		if ($row[0] != "") {
			$fields[$row[0]] = $row;
		}
	}
	\Encoding\PhpArray\Write(CONFIG . "options.php", [
		"fields" => $fields,
		//...
	]);
}

function Select() {
	$fname = CONFIG . "options.php";
	return is_readable($fname)? include $fname : [
		"fields" => [],
	];
}
