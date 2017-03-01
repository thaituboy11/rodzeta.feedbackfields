<?
// TODO упростить и оформить разные типы свойств (привязка к элементам и т.п.)

namespace Citrus\Arealty;

/** @var array $arParams Параметры, чтение, изменение. Не затрагивает одноименный член компонента, но изменения тут влияют на  в файле template.php. */
/** @var array $arResult Результат, чтение/изменение. Затрагивает одноименный член класса компонента. */
/** @var \CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */

$obEnum = new \CUserFieldEnum();
if ($arResult["UF_TYPE"] && ($enum = $obEnum->GetList(array(), array("ID" => $arResult["UF_TYPE"]))->Fetch()))
	$arResult["UF_TYPE_XML_ID"] = $enum["XML_ID"];
else
	$arResult["UF_TYPE_XML_ID"] = false;

// сохраним в кеше компонента
$arResult["SORT_FIELDS"] = SortOrder::setFields($arResult["IBLOCK_ID"], $arResult["UF_SORT_FIELDS"]);

// Колонки таблицы для отображения
// TODO рефакторинг!
if (!$arResult["UF_TYPE_XML_ID"] || $arResult["UF_TYPE_XML_ID"] == 'list' || $arResult["UF_TYPE_XML_ID"] == 'cards')
{
	$iblockFields = null;
	if (empty($arResult["UF_PROP_LINK"]))
	{
		// поля по умолчанию
		$iblockFields = IblockPropertyList::getPropertiesWithCustomFields($arResult["IBLOCK_ID"]);
		$arResult["DISPLAY_COLUMNS"] = array();
        $arResult["DISPLAY_COLUMNS_DEFAULT"] = true;
		$defaultFields = array("~DETAIL_PICTURE", "cost", "address", "rooms", "common_area", "floor");
		foreach ($defaultFields as $field)
		{
			if (substr($field,0,1) == '~')
				$arResult["DISPLAY_COLUMNS"][$field] = $iblockFields[substr($field,1)]["NAME"];
			else
				$arResult["DISPLAY_COLUMNS"][$field] = $iblockFields[Helper::getPropertyIdByCode($arResult["IBLOCK_ID"], $field)]["NAME"];
		}
	}
	else
	{
		// поля, указанные для раздела
		$arResult["DISPLAY_COLUMNS"] = array();
        $arResult["DISPLAY_COLUMNS_DEFAULT"] = false;
		foreach ($arResult["UF_PROP_LINK"] as $propertyId)
		{
			if ($propertyId < 0 && !isset($iblockFields))
				$iblockFields = IblockPropertyList::getPropertiesWithCustomFields($arResult["IBLOCK_ID"]);
			switch ($propertyId)
			{
				case IblockPropertyList::NAME:
					$arResult["DISPLAY_COLUMNS"]["~NAME"] = $iblockFields["NAME"]["NAME"];
					break;
				case IblockPropertyList::DETAIL_PICTURE:
					$arResult["DISPLAY_COLUMNS"]["~DETAIL_PICTURE"] = $iblockFields["DETAIL_PICTURE"]["NAME"];
					break;
				case IblockPropertyList::DATE_CREATE:
					$arResult["DISPLAY_COLUMNS"]["~DATE_CREATE"] = $iblockFields["DATE_CREATE"]["NAME"];
					break;
				default:
					$propertyFields = \CIBlockProperty::GetByID($propertyId)->GetNext();
					$arResult["DISPLAY_COLUMNS"][$propertyFields["CODE"]] = $propertyFields["NAME"];
			}
		}
	}

	if (array_key_exists("address", $arResult["DISPLAY_COLUMNS"]) && array_key_exists("district", $arResult["DISPLAY_COLUMNS"]))
		unset($arResult["DISPLAY_COLUMNS"]["district"]);
}

// FIX for sorted and selected properties
$sectionShowProperties = array(
	15 => array( // kvartiry i komnati
		"market_price",
		"metro",
		"Okrug",
		"region",
		"rooms",
		"common_area",
		"living_area",
		"kitchen_area",
		"floor",
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
if (!empty($sectionShowProperties[$arResult["ID"]])) {
	$tmp = array();
	foreach ($sectionShowProperties[$arResult["ID"]] as $code) {
		$tmp[$code] = $arResult["DISPLAY_COLUMNS"][$code];
	}
	$arResult["DISPLAY_COLUMNS"] = $tmp;
	// FIX common_area
	$arResult["DISPLAY_COLUMNS"]["common_area"] = "Площадь, м2";
	$arResult["DISPLAY_COLUMNS"]["distance_from_mkad"] = "Расстояние от МКАД, км";
}
