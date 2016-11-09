<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

require __DIR__ . "/.init.php";

use \Bitrix\Main\Loader;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Web\HttpClient;

EventManager::getInstance()->addEventHandler("main", "OnBeforeEventAdd",
	function (&$event, &$lid, &$arFields, &$message_id, &$files) {
		// check event type
		if ($event != EVENT_FEEDBACK_FORM) {
			return;
		}
		foreach (Config()["fields"] as $code) {
			if (isset($_POST[$code])) {
				$arFields[$code] = filter_var($_POST[$code], FILTER_SANITIZE_STRING);
			}
		}
	}
);

if (Option::get("rodzeta.feedbackfields", "save_form_data") == "Y") {

	EventManager::getInstance()->addEventHandler("main", "OnBeforeEventSend", function (&$arFields, &$arTemplate) {
		// check event type
		if ($arTemplate["EVENT_NAME"] != EVENT_FEEDBACK_FORM) {
			return;
		}
		Utils::save($arFields, $arTemplate);
	});

}

if (Option::get("rodzeta.feedbackfields", "import_to_bitrix24") == "Y") {

	EventManager::getInstance()->addEventHandler("main", "OnBeforeEventSend",
		function (&$arFields, &$arTemplate) {
			// check event type
			if ($arTemplate["EVENT_NAME"] != EVENT_FEEDBACK_FORM) {
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
		if (\CSite::InDir("/bitrix/") || empty($_REQUEST["success"])) {
			return;
		}
		LocalRedirect(trim(Option::get("rodzeta.feedbackfields", "redirect_url")));
	});

}
