<?php

return (object)[

	"from" => "from@example.org",
	"fromName" => "site.org",
	"site" => "site.org",
	"to" => [
		"admin1@example.org",
		"manager1@example.org",
		"manager2@example.org",
	],

	// Письмо менеджеру
	"subject" => "Заявка {curr_date}",
	"body" => "Данные заявки:

{msg_data}
",

	// Письмо отправляющему заявку
	"subject2" => "Ваша заявка {curr_date}",
	"body2" => "Здравствуйте, {form_name}.
	Ваши данные заявки:

{msg_data}

",

	"msgFields" => [
		"name" => "Имя",
		"phone" => "Телефон",
		"email" => "Электронная почта",

		// hidden
		"object" => "Объект",
		"form_id" => "Метка формы",
	]

];