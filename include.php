<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields;

defined("B_PROLOG_INCLUDED") and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\{Loader, EventManager, Config\Option, Web\HttpClient};

require __DIR__ . "/.init.php";

EventManager::getInstance()->addEventHandler("main", "OnPanelCreate", function () {
	// TODO заменить на определение доступа к редактированию конента
	if (!$GLOBALS["USER"]->IsAdmin()) {
	  return;
	}

	$link = "javascript:" . $GLOBALS["APPLICATION"]->GetPopupLink([
		"URL" => URL_ADMIN,
		"PARAMS" => [
			"resizable" => true,
			//"width" => 780,
			//"height" => 570,
			//"min_width" => 400,
			//"min_height" => 200,
			"buttons" => "[BX.CDialog.prototype.btnClose]"
		]
	]);
  $GLOBALS["APPLICATION"]->AddPanelButton([
		"HREF" => $link,
		"ICON"  => "bx-panel-site-structure-icon",
		//"SRC" => URL_ADMIN . "/icon.gif",
		"TEXT"  => "Доп. поля форм",
		"ALT" => "Доп. поля форм",
		"MAIN_SORT" => 2000,
		"SORT"      => 100
	]);
});

EventManager::getInstance()->addEventHandler("main", "OnBeforeEventAdd",
	function (&$event, &$lid, &$arFields, &$message_id, &$files) {
		// check event type
		if ($event != EVENT_FEEDBACK_FORM) {
			return;
		}
		foreach (Options\Select()["fields"] as $code => $field) {
			if (isset($_POST[$code])) {
				$arFields[$code] = filter_var($_POST[$code], FILTER_SANITIZE_STRING);
			}
		}
	}
);

EventManager::getInstance()->addEventHandler("main", "OnBeforeEventSend", function (&$arFields, &$arTemplate) {
	// check event type
	if ($arTemplate["EVENT_NAME"] != EVENT_FEEDBACK_FORM) {
		return;
	}
	FieldsSave($arFields, $arTemplate);
});

if (Option::get("rodzeta.feedbackfields", "import_to_bitrix24") == "Y") {

	EventManager::getInstance()->addEventHandler("main", "OnBeforeEventSend",
		function (&$arFields, &$arTemplate) {
			// check event type
			if ($arTemplate["EVENT_NAME"] != EVENT_FEEDBACK_FORM) {
				return;
			}

			$client = new HttpClient();
			$postData = [
				"LOGIN" => Option::get("rodzeta.feedbackfields", "bitrix24_login"),
				"PASSWORD" => Option::get("rodzeta.feedbackfields", "bitrix24_password"),
				"TITLE" => $arTemplate["SUBJECT"],
			];
			foreach (Config()["fields_to_bitrix24"] as $dest => $src) {
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
