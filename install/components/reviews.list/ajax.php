<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("NO_AGENT_STATISTIC",true);
define('NO_AGENT_CHECK', true);
define("TRAVELSOFT_REVIEWS_AJAX_CALL", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->IncludeComponent(
        "travelsoft:reviews.list", "bootstrap", $_SESSION["__rwcp"], false
);