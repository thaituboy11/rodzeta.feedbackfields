<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

// bitrix:catalog.section неправильно выводит ссылки с SECTION_CODE_PATH (берет для ссылки текущий раздел вместо реального раздела элемента)
// закрываем косяк стандартного компонента — добавим <link rel="canonical"> с правильной ссылкой (нужного раздела)
// https://support.google.com/webmasters/answer/139066?hl=ru
$arResult["CANONICAL"] = false;
$this->__component->setResultCacheKeys(array("CANONICAL", "DEAL_TYPE"));
if (intval($arResult["ID"]) > 0)
{
	$rsElement = CIBlockElement::GetList(
		Array("SORT" => "ASC"),
		Array("IBLOCK_ID" => $arResult["IBLOCK_ID"], "ID" => $arResult["ID"]),
		$arGroupBy = false,
		$arNavStartParams = false,
		$arSelectFields = Array("ID", "NAME", "DETAIL_PAGE_URL")
	);
	$rsElement->SetUrlTemplates();
	if ($arElement = $rsElement->GetNext())
	{
		$serverName = SITE_SERVER_NAME ? SITE_SERVER_NAME : (COption::GetOptionString('main', 'server_name', $_SERVER['HTTP_HOST']));
		$arResult["CANONICAL"] = $APPLICATION->IsHTTPS() ? 'https://' : 'http://' . $serverName . $arElement["DETAIL_PAGE_URL"];
	}
}

$arResult["CONTACT"] = false;
if ($contact = is_array($arResult["PROPERTIES"]["contact"]) ? $arResult["PROPERTIES"]["contact"]["VALUE"] : false)
	$arResult["CONTACT"] = \Citrus\Arealty\Helper::getContactInfo($contact);

// если контакт для предложения не указан или не найден, выберем первый контакт из списка, будем использовать его
if (!$arResult["CONTACT"])
	$arResult["CONTACT"] = \Citrus\Arealty\Helper::getContactInfo();

if (isset($arResult['PROPERTIES']['deal_type']) && $arResult['PROPERTIES']['deal_type']['VALUE'])
{
	$dealType = $arResult['PROPERTIES']['deal_type']['VALUE_ENUM_ID'];
	$arResult['DEAL_TYPE'] = is_array($dealType) ? reset($dealType) : $dealType;
}

// FIX for sorted and selected properties
$sectionShowProperties = array(
	15 => array( // kvartiry i komnati
		"market_price",
		"metro",
		"Okrug",
		"rooms",
		"floor",
		"common_area",
		"living_area",
		"kitchen_area",
		"balcony",
		"house_type",
		"actually_to",
	),
	18 => array( // zemelnie uchastki
		"market_price",
		"region",
		"land_area",
		"status",
		"road",
		"distance_from_mkad",
		"actually_to",
	),
	20 => array( // doma i kotedgi
		"market_price",
		"region",
		"Ploshad",
		"floors",
		"land_area",
		"actually_to",
	),
	17 => array( // commerch nedvigimost
		"market_price",
		"metro",
		"Okrug",
		"region",
		"common_area", //"area",
		"floor",
		"function",
		"actually_to",
	),
);

if (!empty($sectionShowProperties[$arResult["SECTION"]["ID"]])) {
	$tmp = array();
	foreach ($sectionShowProperties[$arResult["SECTION"]["ID"]] as $code) {
		$tmp[$code] = $arResult["DISPLAY_PROPERTIES"][$code];
	}
	$arResult["DISPLAY_PROPERTIES"] = $tmp;
	$arResult["DISPLAY_PROPERTIES"]["distance_from_mkad"]["NAME"] = "Расстояние от МКАД, км";
}
