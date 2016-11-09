<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields;

define(__NAMESPACE__ . "\APP", __DIR__ . "/");
define(__NAMESPACE__ . "\LIB", __DIR__  . "/lib/");
define(__NAMESPACE__ . "\FILE_OPTIONS", "/upload/.rodzeta.feedbackfields.php");
define(__NAMESPACE__ . "\EVENT_FEEDBACK_FORM", "FEEDBACK_FORM");

require LIB . "encoding/php-array.php";

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

function CreateCache($fields) {
	$basePath = $_SERVER["DOCUMENT_ROOT"];

	$options = [];
	$options["fields"] = array_filter(array_map("trim", $fields));

	\Encoding\PhpArray\Write($basePath . FILE_OPTIONS, $options);
}

function Config() {
	return include $_SERVER["DOCUMENT_ROOT"] . FILE_OPTIONS;
}

function AppendValues($data, $n, $v) {
	for ($i = 0; $i < $n; $i++) {
		$data[] = $v;
	}
	return $data;
}
