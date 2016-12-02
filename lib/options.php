<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields\Options;

use Bitrix\Main\Config\Option;

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
	]);
	Option::set("rodzeta.feedbackfields", $_SERVER["SERVER_NAME"], json_encode([
		"portal_url" => $data["bitrix24_portal_url"],
		"login" => $data["bitrix24_login"],
		"password" => $data["bitrix24_password"],
	]));
}

function Select() {
	$fname = CONFIG . "options.php";
	$result = is_readable($fname)? include $fname : ["fields" => []];
	$result["bitrix24"] = json_decode(Option::get("rodzeta.feedbackfields",  $_SERVER["SERVER_NAME"]), true);
	return $result;
}