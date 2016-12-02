<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields;

define(__NAMESPACE__ . "\ID", "rodzeta.feedbackfields");
define(__NAMESPACE__ . "\URL_ADMIN", "/bitrix/admin/" . ID . "/");
define(__NAMESPACE__ . "\APP", __DIR__ . "/");
define(__NAMESPACE__ . "\LIB", __DIR__  . "/lib/");
define(__NAMESPACE__ . "\CONFIG",
	$_SERVER["DOCUMENT_ROOT"] . "/upload/" . $_SERVER["SERVER_NAME"] . "/." . ID . "/");

define(__NAMESPACE__ . "\EVENT_FEEDBACK_FORM", "FEEDBACK_FORM");

define(__NAMESPACE__ . "\FILE_OPTIONS", "/upload/.rodzeta.feedbackfields.php");
define(__NAMESPACE__ . "\FILE_FORMS", "/upload/.rodzeta.feedbackfields.csv");

require LIB . "encoding/php-array.php";
require LIB . "options.php";

function StorageInit() {
	if (!is_dir(CONFIG)) {
		mkdir(CONFIG, 0700, true);
	}
}

function AppendValues($data, $n, $v) {
	yield from $data;
	for ($i = 0; $i < $n; $i++) {
		yield  $v;
	}
}

function CreateCache($options) {
	$basePath = $_SERVER["DOCUMENT_ROOT"];

	$options["fields"] = array_filter(array_map("trim", $options["fields"]));
	$options["fields_to_file"] = array_filter(array_map("trim", $options["fields_to_file"]));

	$tmp = [];
	foreach ($options["fields_to_bitrix24"] as $v) {
		$v["BITRIX24"] = trim($v["BITRIX24"]);
		$v["FIELD"] = trim($v["FIELD"]);
		if ($v["BITRIX24"] == "" || $v["FIELD"] == "" ) {
			continue;
		}
		$tmp[$v["BITRIX24"]] = $v["FIELD"];
	}
	$options["fields_to_bitrix24"] = $tmp;

	\Encoding\PhpArray\Write($basePath . FILE_OPTIONS, $options);
}

function Config() {
	return include $_SERVER["DOCUMENT_ROOT"] . FILE_OPTIONS;
}

function SaveFields($arFields, $arTemplate) {
	$config = Config();
	if (empty($config["fields_to_file"])) {
		return;
	}
	$row = [
		date("Y-m-d H:i:s"),
		$arTemplate["ID"],
		$arTemplate["SUBJECT"],
	];
	foreach ($config["fields_to_file"] as $code) {
		$row[] = isset($arFields[$code])? $arFields[$code] : "";
	}
	$fp = fopen($_SERVER["DOCUMENT_ROOT"] . FILE_FORMS, "a");
	if ($fp) {
		fputcsv($fp, $row, "\t");
		fclose($fp);
	}
}
