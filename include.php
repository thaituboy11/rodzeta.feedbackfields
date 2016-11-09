<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use \Bitrix\Main\Loader;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Web\HttpClient;

EventManager::getInstance()->addEventHandler("main", "OnBeforeEventAdd",
	function (&$event, &$lid, &$arFields, &$message_id, &$files) {
		// check event type
		if ($event != "FEEDBACK_FORM") {
			return;
		}
		foreach (json_decode(Option::get("rodzeta.feedbackfields", "fields", "[]")) as $code) {
			if (isset($_POST[$code])) {
				$arFields[$code] = filter_var($_POST[$code], FILTER_SANITIZE_STRING);
			}
		}
	}
);

if (Option::get("rodzeta.feedbackfields", "save_form_data") == "Y") {

	EventManager::getInstance()->addEventHandler("main", "OnBeforeEventSend", function (&$arFields, &$arTemplate) {
		// check event type
		if ($arTemplate["EVENT_NAME"] != "FEEDBACK_FORM") {
			return;
		}
		\Rodzeta\Feedbackfields\Utils::save($arFields, $arTemplate);
	});

}

if (Option::get("rodzeta.feedbackfields", "import_to_bitrix24") == "Y") {

	EventManager::getInstance()->addEventHandler("main", "OnBeforeEventSend",
		function (&$arFields, &$arTemplate) {
			// check event type
			if ($arTemplate["EVENT_NAME"] != "FEEDBACK_FORM") {
				return;
			}

			$fields = parse_ini_string(Option::get("rodzeta.feedbackfields", "bitrix24_fields"));
			$client = new HttpClient();
			$postData = array(
				"LOGIN" => Option::get("rodzeta.feedbackfields", "bitrix24_login"),
				"PASSWORD" => Option::get("rodzeta.feedbackfields", "bitrix24_password"),
				"TITLE" => $arTemplate["SUBJECT"],
			);
			foreach ($fields as $dest => $src) {
				if (isset($arFields[$src])) {
					$postData[$dest] = $arFields[$src];
				}
			}
			$response = $client->post(
				"https://" . Option::get("rodzeta.feedbackfields", "bitrix24_portal_url") . "/crm/configs/import/lead.php",
				$postData
			);
		}
	);
}

if (Option::get("rodzeta.feedbackfields", "use_redirect") == "Y"
		&& trim(Option::get("rodzeta.feedbackfields", "redirect_url")) != "") {

	EventManager::getInstance()->addEventHandler("main", "OnBeforeProlog", function () {
		if (CSite::InDir("/bitrix/") || empty($_REQUEST["success"])) {
			return;
		}
		LocalRedirect(trim(Option::get("rodzeta.feedbackfields", "redirect_url")));
	});

}
