<?php

\Bitrix\Main\Loader::includeModule("iblock");

$classes = array(
    
    "travelsoft\\reviews\\Statistics" => "lib/Statistics.php"
);
CModule::AddAutoloadClasses("travelsoft.reviews", $classes);