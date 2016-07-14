<?php
/***********************************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Feedbackfields;

use \Bitrix\Main\Config\Option;

final class Utils {

	const SRC_NAME = "/upload/rodzeta.feedbackfields.csv";

	static function save($arFields) {
		$fields = array_map("trim", explode("\n", Option::get("rodzeta.feedbackfields", "saved_fields")));
		if (count($fields) == 0) {
			return;
		}

		$row = array();
		foreach ($fields as $code) {
			$row[] = isset($arFields[$code])? $arFields[$code] : "";
		}
		$fp = fopen($_SERVER["DOCUMENT_ROOT"] . self::SRC_NAME, "a");
		if ($fp) {
			fputcsv($fp, $row, "\t");
			fclose($fp);
		}
	}

}