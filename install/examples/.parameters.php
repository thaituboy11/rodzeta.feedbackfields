<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"CUSTOM_SORT_DATA" => \Rodzeta\Feedbackfields\SortableParameter(
		$arCurrentValues["CUSTOM_SORT_DATA"],
		"CUSTOM_SORT_DATA"
	)
);