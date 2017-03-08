<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

define(__NAMESPACE__ . "\ID", "rodzeta.feedbackfields");
define(__NAMESPACE__ . "\URL_ADMIN", "/bitrix/admin/" . ID);
define(__NAMESPACE__ . "\APP", dirname(__DIR__) . "/");
define(__NAMESPACE__ . "\LIB", APP  . "/lib/");

define(__NAMESPACE__ . "\SITE", substr($_SERVER["SERVER_NAME"], 0, 4) == "www."?
	substr($_SERVER["SERVER_NAME"], 4) : $_SERVER["SERVER_NAME"]);
define(__NAMESPACE__ . "\CONFIG",
	$_SERVER["DOCUMENT_ROOT"] . "/upload/." . ID . "." . SITE);

define(__NAMESPACE__ . "\FILE_OPTIONS", CONFIG . ".php");
define(__NAMESPACE__ . "\FILE_FORMS", CONFIG . ".formresults.csv");

define(__NAMESPACE__ . "\EVENT_FEEDBACK_FORM", "FEEDBACK_FORM");

Loc::loadMessages(__FILE__);

require LIB . "encoding/php-array.php";
require LIB . "collection/.init.php";

function OptionsUpdate($data) {
	$fields = array();
	$fieldsBitrix24 = array();
	$fieldsCsv = array();
	foreach ($data["fields"] as $row) {
		$row = array_map("trim", $row);
		if ($row[0] != "") {
			$fields[$row[0]] = $row;
			if ($row[3] != "") {
				$fieldsBitrix24[$row[3]][$row[0]] = 1;
			}
			if ($row[2] == "Y") {
				$fieldsCsv[$row[0]] = 1;
			}
		}
	}
	\Encoding\PhpArray\Write(FILE_OPTIONS, array(
		"fields" => $fields,
		"fields_bitrix24" => $fieldsBitrix24,
		"fields_csv" => $fieldsCsv,
	));
	Option::set("rodzeta.feedbackfields", $_SERVER["SERVER_NAME"], json_encode(array(
		"portal_url" => $data["bitrix24_portal_url"],
		"login" => $data["bitrix24_login"],
		"password" => $data["bitrix24_password"],
	)));
}

function OptionsSelect() {
	$result = is_readable(FILE_OPTIONS)? include FILE_OPTIONS : array(
		"fields" => array(),
		"fields_bitrix24" => array(),
		"fields_csv" => array(),
	);
	$result["bitrix24"] = json_decode(Option::get("rodzeta.feedbackfields",  $_SERVER["SERVER_NAME"]), true);
	return $result;
}

function FieldsSave($arFields, $arTemplate) {
	$options = OptionsSelect();
	$fields = $options["fields_csv"];
	if (empty($fields)) {
		return;
	}
	$row = array(
		date("Y-m-d H:i:s"),
		$arTemplate["ID"],
		$arTemplate["SUBJECT"],
	);
	foreach ($fields as $code => $v) {
		$row[] = isset($arFields[$code])? $arFields[$code] : "";
	}
	$fp = fopen(FILE_FORMS, "a");
	if ($fp) {
		fputcsv($fp, $row, "\t");
		fclose($fp);
	}
}

function SortableParameter($selectedValues, $key) {
	$options = OptionsSelect();
	$fields = array();
	foreach ($selectedValues as $k => $v) {
		$fields[] = array($k, $v, true);
		unset($options["fields"][$k]);
	}
	foreach ($options["fields"] as $k => $v) {
		$fields[] = array($k, $v[1], false);
	}
	$baseUrl = "/bitrix/tools/" . ID;
	return array(
		"NAME" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_SORTABLE_PARAMETER_TITLE"),
		"TYPE" => "CUSTOM",
		"JS_FILE" => $baseUrl . ".settings.js",
		"JS_EVENT" => "RodzetaFeedbackfields_SortableParameterEdit",
		"JS_DATA" => json_encode(array(
			"fields" => $fields,
			"baseUrl" => $baseUrl,
		)),
		"DEFAULT" => null,
		"PARENT" => "BASE",
	);
}

function ForTemplate($orderedFields, $arResult) {
	$result = array();
	foreach ($orderedFields as $code => $title) {
		$tmp = !empty($arResult[$code])? $arResult[$code]
			: (!empty($_POST[$code])? $_POST[$code] : "");
		$result[$code] = array(
			"CODE" => $code,
			"~NAME" => $title,
			"NAME" => htmlspecialchars($title),
			"~VALUE" => $tmp,
			"VALUE" => htmlspecialchars($tmp),
		);
		if ($code == "TEXT" || substr($code, -5) == "_TEXT") {
			$result[$code]["TYPE"] = "TEXT";
			$result[$code]["HTML"] =
				'<textarea name="' . $result[$code]["CODE"] . '">'
					. $result[$code]["VALUE"] . '</textarea>';
		} else {
			$result[$code]["HTML"] =
				'<input type="text" name="' . $result[$code]["CODE"]
					. '" value="' . $result[$code]["VALUE"] . '">';
		}
	}
	return $result;
}
