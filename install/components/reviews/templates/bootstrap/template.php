<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$this->setFrameMode(true);

Bitrix\Main\Page\Asset::getInstance()->addCss("/local/modules/travelsoft.reviews/plugins/fancybox/jquery.fancybox.min.css", true);
Bitrix\Main\Page\Asset::getInstance()->addCss("/local/modules/travelsoft.reviews/plugins/bootstrap/css/bootstrap.min.css", true);
Bitrix\Main\Page\Asset::getInstance()->addCss("/local/modules/travelsoft.reviews/plugins/bootstrap/css/bootstrap-theme.min.css", true);
Bitrix\Main\Page\Asset::getInstance()->addJs("/local/modules/travelsoft.reviews/plugins/jquery-3.2.1.min.js", true);
Bitrix\Main\Page\Asset::getInstance()->addJs("/local/modules/travelsoft.reviews/plugins/bootstrap/js/bootstrap.min.js", true);
Bitrix\Main\Page\Asset::getInstance()->addJs("/local/modules/travelsoft.reviews/plugins/raty/jquery.raty.min.js", true);
Bitrix\Main\Page\Asset::getInstance()->addJs("/local/modules/travelsoft.reviews/plugins/fancybox/jquery.fancybox.min.js", true);
Bitrix\Main\Page\Asset::getInstance()->addJs("/local/modules/travelsoft.reviews/plugins/readmore.min.js", true);
?>

<? if ($arParams["SHOW_STATISTICS"] === "Y"): ?>

    <?

    $APPLICATION->IncludeComponent(
            "travelsoft:reviews.statistics", "bootstrap", Array(
        "LINK_ELEMENT_ID" => $arParams["LINK_ELEMENT_ID"]
            ), false
    );
    ?>

<? endif ?>

<?

$APPLICATION->IncludeComponent(
        "travelsoft:reviews.add", "bootstrap", array(
    "LINK_ELEMENT_ID" => $arParams["LINK_ELEMENT_ID"],
    "NEED_PREMODERATION" => $arParams["NEED_PREMODERATION"],
    "SHOW_RATING_FIELD" => $arParams["SHOW_RATING_FIELD"],
    "SHOW_ADD_IMAGE_FIELD" => $arParams["SHOW_ADD_IMAGE_FIELD"]
        ), false
);
?>


<?

$APPLICATION->IncludeComponent(
        "travelsoft:reviews.list", "bootstrap", array(
    "LINK_ELEMENT_ID" => $arParams["LINK_ELEMENT_ID"],
    "PAGE_SIZE" => $arParams["PAGE_SIZE"]
        ), false
);
?>