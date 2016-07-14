<?php
/***********************************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\String;
use Bitrix\Main\Loader;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_MAIN_TAB_TITLE_SET"),
  ),
));

?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.feedbackfields", "fields", $request->getPost("fields"));
		Option::set("rodzeta.feedbackfields", "save_form_data", $request->getPost("save_form_data"));
		Option::set("rodzeta.feedbackfields", "saved_fields", $request->getPost("saved_fields"));

		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}
}

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?> type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Список кодов для дополнительных полей</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<textarea name="fields" rows="10"
				placeholder="например
USER_PHONE
USER_REGION
USER_ADDRESS
..."><?= Option::get("rodzeta.feedbackfields", "fields") ?></textarea>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Сохранять данные форм в файл</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="save_form_data" value="Y" type="checkbox"
				<?= Option::get("rodzeta.feedbackfields", "save_form_data") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Список кодов полей сохраняемых в файл</label><br>
			<b><a target="_blank"
				href="/bitrix/admin/fileman_file_edit.php?path=<?= urlencode(\Rodzeta\Feedbackfields\Utils::SRC_NAME) ?>">rodzeta.feedbackfields.csv</a>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<textarea name="saved_fields" rows="10"
				placeholder="например
AUTHOR
AUTHOR_EMAIL
TEXT
USER_REGION
USER_ADDRESS
..."><?= Option::get("rodzeta.feedbackfields", "saved_fields") ?></textarea>
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<?php

$tabControl->end();
