<?php

/**
 * Компонет добавления отзыва
 * 
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftReviewsStatistics extends CBitrixComponent {

    public function executeComponent() {

        global $APPLICATION, $USER;

        $module_id = "travelsoft.reviews";

        

        $this->IncludeComponentTemplate();
    }
    
}
