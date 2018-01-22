<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters["PARAMETERS"]['LINK_ELEMENT_ID'] = array(
            "PARENT" => "BASE",
            "NAME" => "ID элемента",
            "TYPE" => "STRING"
        );

$arComponentParameters["PARAMETERS"]['ELEMENT_COUNT'] = array(
            "PARENT" => "BASE",
            "NAME" => "Количество элементов на странице",
            "TYPE" => "STRING",
            "DEFAULT" => "10"
        );
