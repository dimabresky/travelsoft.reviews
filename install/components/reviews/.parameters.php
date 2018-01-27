<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$arComponentParameters["PARAMETERS"]['LINK_ELEMENT_ID'] = array(
            "PARENT" => "BASE",
            "NAME" => "ID элемента для отзыва",
            "TYPE" => "STRING"
        );

$arComponentParameters["PARAMETERS"]['SHOW_RATING_FIELD'] = array(
            "PARENT" => "BASE",
            "NAME" => "Показывать поле для рейтинга",
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        );

$arComponentParameters["PARAMETERS"]['SHOW_ADD_IMAGE_FIELD'] = array(
            "PARENT" => "BASE",
            "NAME" => "Добавить возможность загрузки фото",
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        );

$arComponentParameters["PARAMETERS"]['NEED_PREMODERATION'] = array(
            "PARENT" => "BASE",
            "NAME" => "Нужна премодерация отзывов",
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        );

$arComponentParameters["PARAMETERS"]['PAGE_SIZE'] = array(
            "PARENT" => "BASE",
            "NAME" => "Количество элементов на странице",
            "TYPE" => "STRING",
            "DEFAULT" => "10"
        );

$arComponentParameters["PARAMETERS"]['SHOW_STATISTICS'] = array(
            "PARENT" => "BASE",
            "NAME" => "Показывать статистику по отзывам",
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        );