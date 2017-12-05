<?php

namespace travelsoft\reviews;

/* 
 * Функции модуля
 */

\Bitrix\Main\Loader::includeModule("iblock");

function getStatistics (int $element_id, int $stars = 1, string $date_from, string $date_to) {
    
    static $rewiewsList = array();
    
    
}

