<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\EventManager;
use Bitrix\Main\Web\HttpClient;

Loc::loadMessages(__FILE__);

require __DIR__ . "/lib/.init.php";

function init() {
	EventManager::getInstance()->addEventHandler("main", "OnPanelCreate", function () {
		// TODO use rights "can edit content"
		if (!$GLOBALS["USER"]->IsAdmin()) {
		  return;
		}

		$link = "javascript:" . $GLOBALS["APPLICATION"]->GetPopupLink(array(
			"URL" => URL_ADMIN . ".php",
			"PARAMS" => array(
				"resizable" => true,
				//"width" => 780,
				//"height" => 570,
				//"min_width" => 400,
				//"min_height" => 200,
				"buttons" => "[BX.CDialog.prototype.btnClose]"
			)
		));
	  $GLOBALS["APPLICATION"]->AddPanelButton(array(
			"HREF" => $link,
			"ICON" => "bx-panel-site-structure-icon",
			//"SRC" => URL_ADMIN . "/icon.gif",
			"TEXT" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_OPTIONS"),
			"ALT" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_OPTIONS"),
			"MAIN_SORT" => 2000,
			"SORT" => 100
		));
	});

	EventManager::getInstance()->addEventHandler("main", "OnBeforeEventAdd",
		function (&$event, &$lid, &$arFields, &$message_id, &$files) {
			// check event type
			if ($event != EVENT_FEEDBACK_FORM) {
				return;
			}
			$options = OptionsSelect();
			foreach ($options["fields"] as $code => $v) {
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

	EventManager::getInstance()->addEventHandler("main", "OnBeforeEventSend",
		function (&$arFields, &$arTemplate) {
			// check event type
			if ($arTemplate["EVENT_NAME"] != EVENT_FEEDBACK_FORM) {
				return;
			}
			$currentOptions = OptionsSelect();
			if ($currentOptions["bitrix24"]["portal_url"] == ""
					|| $currentOptions["bitrix24"]["login"] == ""
					|| $currentOptions["bitrix24"]["password"] == "") {
				return;
			}
			$client = new HttpClient();
			$postData = array(
				"LOGIN" => $currentOptions["bitrix24"]["login"],
				"PASSWORD" => $currentOptions["bitrix24"]["password"],
				"TITLE" => $arTemplate["SUBJECT"],
			);
			foreach ($currentOptions["fields_bitrix24"] as $dest => $src) {
				$tmp = array();
				// collect all input values for bitrix24 lead field
				foreach ($src as $k => $v) {
					if (isset($arFields[$k])) {
						$tmp[] = $arFields[$k];
					}
				}
				$postData[$dest] = implode("\n\n", $tmp);
			}
			$response = $client->post(
				"https://" . $currentOptions["bitrix24"]["portal_url"]
					. "/crm/configs/import/lead.php",
				$postData
			);
		}
	);

	/*
	if (Option::get("rodzeta.feedbackfields", "use_redirect") == "Y"
			&& trim(Option::get("rodzeta.feedbackfields", "redirect_url")) != "") {

		EventManager::getInstance()->addEventHandler("main", "OnBeforeProlog", function () {
			if (\CSite::InDir("/bitrix/") || empty($_REQUEST["success"])) {
				return;
			}
			LocalRedirect(trim(Option::get("rodzeta.feedbackfields", "redirect_url")));
		});

	}
	*/
}

init();
