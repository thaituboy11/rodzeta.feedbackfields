<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

// NOTE this file must compatible with php 5.3

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class rodzeta_feedbackfields extends CModule {
	var $MODULE_ID = "rodzeta.feedbackfields"; // NOTE using "var" for bitrix rules

	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_GROUP_RIGHTS;
	public $PARTNER_NAME;
	public $PARTNER_URI;

	//public $MODULE_GROUP_RIGHTS = 'N';
	//public $NEED_MAIN_VERSION = '';
	//public $NEED_MODULES = array();

	function __construct() {
		$this->MODULE_ID = "rodzeta.feedbackfields"; // NOTE for showing module in /bitrix/admin/partner_modules.php?lang=ru

		$arModuleVersion = array();
		include __DIR__ . "/version.php";

		if (!empty($arModuleVersion["VERSION"])) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = Loc::getMessage("RODZETA_FEEDBACKFIELDS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("RODZETA_FEEDBACKFIELDS_MODULE_DESCRIPTION");
		$this->MODULE_GROUP_RIGHTS = "N";

		$this->PARTNER_NAME = "Rodzeta";
		$this->PARTNER_URI = "http://rodzeta.ru/";
	}

	function InstallFiles() {
		$path = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/";
		$modulePath = $_SERVER["DOCUMENT_ROOT"]
			. "/bitrix/modules/" . $this->MODULE_ID;
		CopyDirFiles(
			$modulePath . "/install/admin/",
			$path,
			true,
			true
		);
		$path = dirname($path) . "/tools/";
		copy($modulePath . "/install/tools/" . $this->MODULE_ID . ".settings.js", $path . $this->MODULE_ID . ".settings.js");
		copy($modulePath . "/install/tools/" . $this->MODULE_ID . ".sortable.js", $path . $this->MODULE_ID . ".sortable.js");

		CopyDirFiles(
	    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/templates",
	    $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default",
	    true, true
    );
    
		return true;
	}

	function UninstallFiles() {
		$path = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/";
		unlink($path . $this->MODULE_ID . ".php");
		$path = dirname($path) . "/tools/";
		unlink($path . $this->MODULE_ID . ".settings.js");
		unlink($path . $this->MODULE_ID . ".sortable.js");
		return true;
	}

	function DoInstall() {
		RegisterModule($this->MODULE_ID);
		RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID);
		$this->InstallFiles();
	}

	function DoUninstall() {
		$this->UninstallFiles();
		UnRegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID);
		UnRegisterModule($this->MODULE_ID);
	}
}
