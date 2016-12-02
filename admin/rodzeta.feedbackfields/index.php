<?php
/*******************************************************************************
 * rodzeta.feedbackfields - Additional fields for feedback form
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Feedbackfields;

use Bitrix\Main\{Application, Config\Option, Localization\Loc};

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
//require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

// TODO заменить на определение доступа к редактированию контента
// 	if (!$USER->CanDoOperation("rodzeta.siteoptions"))
if (!$GLOBALS["USER"]->IsAdmin()) {
	//$APPLICATION->authForm("ACCESS DENIED");
  return;
}

Loc::loadMessages(__FILE__);
//Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . ID . "/admin/" . ID . "/index.php");

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

//StorageInit();

$formSaved = check_bitrix_sessid() && $request->isPost();
if ($formSaved) {
	echo "<pre>"; print_r($request->getPostList()); echo "</pre>";
	//Update($request->getPostList());
}

$config = Config();
$currentOptions = []; //$config["fields"]; //Select();
$currentOptions = array_merge([
	"USER_REGION" => ["USER_REGION", "Регион"],
	"USER_PHONE" => ["USER_PHONE", "Телефон"],
	"USER_SITE" => ["USER_SITE", "Сайт"],
], $currentOptions);

?>

<form action="" method="post">
	<?= bitrix_sessid_post() ?>

	<div class="adm-detail-title">Список кодов для дополнительных полей</div>

	<table width="100%" class="js-table-autoappendrows">
		<tbody>
			<?php $i = 0; foreach (AppendValues($currentOptions, 5, ["", ""]) as $i => $field) { $i++; ?>
				<tr data-idx="<?= $i ?>">
					<td>
						<input type="text" placeholder="Код поля"
							name="fields[<?= $i ?>][0]"
							value="<?= htmlspecialcharsex($field[0]) ?>"
							style="width:96%;">
					</td>
					<td>
						<input type="text" placeholder="Название поля"
							name="fields[<?= $i ?>][1]"
							value="<?= htmlspecialcharsex($field[1]) ?>"
							style="width:96%;">
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>


</form>

<?php if (0 && $formSaved) { ?>

	<script>
		// close after submit
		top.BX.WindowManager.Get().AllowClose();
		top.BX.WindowManager.Get().Close();
	</script>

<?php } else { ?>

	<script>
		// add buttons for current windows
		BX.WindowManager.Get().SetButtons([
			BX.CDialog.prototype.btnSave,
			BX.CDialog.prototype.btnCancel
			//,BX.CDialog.prototype.btnClose
		]);
	</script>

<?php } ?>

<script>

BX.ready(function () {
	"use strict";

	// init options
	//...

	// autoappend rows
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
					for (let $input of $trLast.querySelectorAll("input,select")) {
						let name = $input.getAttribute("name");
						if (name) {
							$input.setAttribute("name", name.replace(/([a-zA-Z0-9])\[\d+\]/, "$1[" + idx + "]"));
						}
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
