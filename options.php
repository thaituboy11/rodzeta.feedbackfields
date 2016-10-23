<?php
/***********************************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

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

		Option::set("rodzeta.feedbackfields", "import_to_bitrix24", $request->getPost("import_to_bitrix24"));
		Option::set("rodzeta.feedbackfields", "bitrix24_fields", $request->getPost("bitrix24_fields"));
		Option::set("rodzeta.feedbackfields", "bitrix24_login", $request->getPost("bitrix24_login"));
		Option::set("rodzeta.feedbackfields", "bitrix24_password", $request->getPost("bitrix24_password"));
		Option::set("rodzeta.feedbackfields", "bitrix24_portal_url", $request->getPost("bitrix24_portal_url"));

		Option::set("rodzeta.feedbackfields", "use_redirect", $request->getPost("use_redirect"));
		Option::set("rodzeta.feedbackfields", "redirect_url", $request->getPost("redirect_url"));

		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}
}

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr class="heading">
		<td colspan="2">Настройки дополнительных полей формы</td>
	</tr>

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

	<tr class="heading">
		<td colspan="2">Настройки сохранения данных формы в <a
			target="_blank"
			href="/bitrix/admin/fileman_file_edit.php?path=<?= urlencode(\Rodzeta\Feedbackfields\Utils::SRC_NAME) ?>">csv-файл</a></td>
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

	<tr class="heading">
		<td colspan="2">Настройки импорта данных формы в Bitrix24</td>
	</tr>

  <tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Импортировать данные форм в Bitrix24</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="import_to_bitrix24" value="Y" type="checkbox"
				<?= Option::get("rodzeta.feedbackfields", "import_to_bitrix24") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				Список соответствий вида<br>
				<b>поле лида=поле формы</b><br>
				см. <a href="https://dev.1c-bitrix.ru/community/blogs/chaos/crm-sozdanie-lidov-iz-drugikh-servisov.php" target="_blank">Полный список возможных полей</a>
			</label>

		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<textarea name="bitrix24_fields" rows="10" cols="40"
				placeholder="например
NAME=AUTHOR
PHONE_MOBILE=USER_PHONE
WEB_WORK=USER_SITE
COMMENTS=TEXT
EMAIL_WORK=AUTHOR_EMAIL
ADDRESS=USER_ADDRESS
..."><?= Option::get("rodzeta.feedbackfields", "bitrix24_fields") ?></textarea>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Адрес портала Bitrix24</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input type="text" size="30" name="bitrix24_portal_url" value="<?= Option::get("rodzeta.feedbackfields", "bitrix24_portal_url") ?>">
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>LOGIN пользователя-"лидогенератора"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input type="text" size="30" name="bitrix24_login" value="<?= Option::get("rodzeta.feedbackfields", "bitrix24_login") ?>">
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>PASSWORD пользователя-"лидогенератора"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="bitrix24_password" size="30" type="password"
				readonly
    		onfocus="this.removeAttribute('readonly')"
    		value="<?= Option::get("rodzeta.feedbackfields", "bitrix24_password") ?>">
		</td>
	</tr>

	<tr class="heading">
		<td colspan="2">Настройки редиректа после отправки формы</td>
	</tr>

  <tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Использовать редирект после отправки формы</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="use_redirect" value="Y" type="checkbox"
				<?= Option::get("rodzeta.feedbackfields", "use_redirect") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Урл по умолчанию для редиректа</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input type="text" size="30" name="redirect_url" value="<?= Option::get("rodzeta.feedbackfields", "redirect_url") ?>">
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<?php

$tabControl->end();
