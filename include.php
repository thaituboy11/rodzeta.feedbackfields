<?php
/***********************************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;

EventManager::getInstance()->addEventHandler("main", "OnBeforeEventAdd",
	function (&$event, &$lid, &$arFields, &$message_id, &$files) {
		// check event type
		if ($event != "FEEDBACK_FORM") {
			return;
		}
		$fields = array_filter(array_map("trim", explode("\n", Option::get("rodzeta.feedbackfields", "fields"))));
		foreach ($fields as $code) {
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