<?php

return function ($config, $params = [], $callback = null) {
	$mail = new PHPMailer();
	$mail->CharSet = "UTF-8";

	//$mail->isSMTP();
	//$mail->Host = 'smtp.yandex.ru';
	//$mail->SMTPAuth = true;
	//$mail->Username = 'i1111';
	//$mail->Password = 'j111';
	//$mail->Port = '587';
	//$mail->SMTPSecure = 'SSL';
	//$mail->Port = '465';

	$mail->From = $config->from;
	$mail->FromName = $config->fromName;
	if (is_array($config->to)) {
		foreach ($config->to as $k => $v) {
			$mail->addAddress($v);
		}
	} else {
		$mail->addAddress($config->to);
	}

	// build template params
	$msgData = [];
	$vars = [
		"{site}" => $config->site,
		"{curr_date}" => date("Y-m-d H:i:s"),
	];
	foreach ($config->msgFields as $field => $title) {
		$v = filter_var($_POST[$field], FILTER_SANITIZE_SPECIAL_CHARS);
		$vars["{form_" . $field . "}"] = $v;
		$msgData[] = "[" . $title . "]: " . $v;
	}
	$vars["{msg_data}"] = implode("\n", $msgData);
	$tags = array_keys($vars);
	$values = array_values($vars);

	$mail->Subject = str_replace($tags, $values, $config->subject);
	$mail->Body = str_replace($tags, $values, $config->body);

	if (is_callable($callback)) {
		$mail = $callback($mail, $config, $params);
	}

	$result = !$mail->send()?
		["error" => $mail->ErrorInfo] :
		["error" => false];
	if (isset($config->msgFields["email"]) && !empty($config->subject2)
				&& $result["error"] === false) {
		// send second email
		$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
		if (!empty(trim($email))) {
			$mail->ClearAddresses();
			$mail->ClearAttachments();
			$mail->addAddress($email);
			$mail->Subject = str_replace($tags, $values, $config->subject2);
			$mail->Body = str_replace($tags, $values, $config->body2);
			$result2 = !$mail->send()?
				["error" => $mail->ErrorInfo] :
				["error" => false];
		}
	}

	return $result;
};
