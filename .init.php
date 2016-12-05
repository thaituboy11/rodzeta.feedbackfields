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
define(__NAMESPACE__ . "\FILE_FORMS", CONFIG . ".form_results.csv");

define(__NAMESPACE__ . "\EVENT_FEEDBACK_FORM", "FEEDBACK_FORM");

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

function FieldsSave($arFields, $arTemplate) {
	$fields = Options\Select()["fields_csv"];
	if (empty($fields)) {
		return;
	}
	$row = [
		date("Y-m-d H:i:s"),
		$arTemplate["ID"],
		$arTemplate["SUBJECT"],
	];
	foreach ($fields as $code => $v) {
		$row[] = isset($arFields[$code])? $arFields[$code] : "";
	}
	$fp = fopen(FILE_FORMS, "a");
	if ($fp) {
		fputcsv($fp, $row, "\t");
		fclose($fp);
	}
}
