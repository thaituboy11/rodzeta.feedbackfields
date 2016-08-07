<?php

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

// for captcha use https://github.com/dapphp/securimage
//session_start();
//require $_SERVER["DOCUMENT_ROOT"] . "/securimage/securimage.php";
//$securimage = new Securimage();

$valid = !empty($securimage)?
	($securimage->check($_POST["captcha_code"]) != false) :	true;
if ($valid) {
  $sendForm = include dirname(__DIR__) . "/init.php";
	$result = $sendForm(
		include "config.php" /*,
		[],
		function ($mail, $config, $params) {
			$mail->AddAttachment($_SERVER["DOCUMENT_ROOT"] . "/tmp/price.html", "price.html");
			$mail->AddAttachment($_SERVER["DOCUMENT_ROOT"] . "/tmp/price-2.html", "price-2.html");
			return $mail;
		}*/
	);
} else {
	$result = ["error" => "Неправильный код капчи"];
}

echo json_encode($result);
