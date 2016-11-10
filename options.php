<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields;

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

$tabControl = new \CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_MAIN_TAB_TITLE_SET"),
  ),
  array(
		"DIV" => "edit2",
		"TAB" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_FILE_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_FILE_TAB_TITLE_SET"),
  ),
  array(
		"DIV" => "edit3",
		"TAB" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_BITRIX24_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_BITRIX24_TAB_TITLE_SET"),
  ),
));

?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.feedbackfields", "save_form_data", $request->getPost("save_form_data"));

		Option::set("rodzeta.feedbackfields", "import_to_bitrix24", $request->getPost("import_to_bitrix24"));
		Option::set("rodzeta.feedbackfields", "bitrix24_fields", $request->getPost("bitrix24_fields"));
		Option::set("rodzeta.feedbackfields", "bitrix24_login", $request->getPost("bitrix24_login"));
		Option::set("rodzeta.feedbackfields", "bitrix24_password", $request->getPost("bitrix24_password"));
		Option::set("rodzeta.feedbackfields", "bitrix24_portal_url", $request->getPost("bitrix24_portal_url"));

		Option::set("rodzeta.feedbackfields", "use_redirect", $request->getPost("use_redirect"));
		Option::set("rodzeta.feedbackfields", "redirect_url", $request->getPost("redirect_url"));

		CreateCache([
			"fields" => $request->getPost("fields"),
			"fields_to_file" => $request->getPost("fields_to_file")
		]);

		\CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_FEEDBACKFIELDS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}
}

$config = Config();

$tabControl->begin();

?>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr valign="top">
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Список кодов для дополнительных полей</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">

			<table width="100%" class="js-table-autoappendrows">
				<tbody>
					<?php foreach (AppendValues($config["fields"], 5, "") as $i => $fieldCode) { ?>
						<tr data-idx="<?= $i ?>">
							<td>
								<input name="fields[<?= $i ?>]" type="text" placeholder="USER_FIELD"
									value="<?= htmlspecialcharsex($fieldCode) ?>">
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

		</td>
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

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				Сохранять в файл
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<input name="save_form_data" value="Y" type="checkbox"
				<?= Option::get("rodzeta.feedbackfields", "save_form_data") == "Y"? "checked" : "" ?>>
		</td>
	</tr>

	<tr valign="top">
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Список кодов полей</label><br>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">

			<table width="100%" class="js-table-autoappendrows">
				<tbody>
					<?php foreach (AppendValues($config["fields_to_file"], 5, "") as $i => $fieldCode) { ?>
						<tr data-idx="<?= $i ?>">
							<td>
								<input name="fields_to_file[<?= $i ?>]" type="text" placeholder="USER_FIELD"
									value="<?= htmlspecialcharsex($fieldCode) ?>">
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

		</td>
	</tr>

	<?php $tabControl->beginNextTab() ?>

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

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<script>

BX.ready(function () {
	"use strict";

	function makeAutoAppend($table) {
		function bindEvents($row) {
			for (let $input of $row.querySelectorAll('input[type="text"]')) {
				$input.addEventListener("change", function (event) {
					let $tr = event.target.closest("tr");
					let $trLast = $table.rows[$table.rows.length - 1];
					if ($tr != $trLast) {
						return;
					}
					$table.insertRow(-1);
					$trLast = $table.rows[$table.rows.length - 1];
					$trLast.innerHTML = $tr.innerHTML;
					let idx = parseInt($tr.getAttribute("data-idx")) + 1;
					$trLast.setAttribute("data-idx", idx);
					for (let $input of $trLast.querySelectorAll('input[type="text"]')) {
						$input.setAttribute("name", $input.getAttribute("name").replace(/([a-zA-Z0-9])\[\d+\]/, "$1[" + idx + "]"));
					}
					bindEvents($trLast);
				});
			}
		}
		for (let $row of document.querySelectorAll(".js-table-autoappendrows tr")) {
			bindEvents($row);
		}
	}

	for (let $table of document.querySelectorAll(".js-table-autoappendrows")) {
		makeAutoAppend($table);
	}

});

</script>

<?php

$tabControl->end();
