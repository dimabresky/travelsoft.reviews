<?php

/**
 * Компонет списка отзывов
 * 
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftReviewsList extends CBitrixComponent {

    public function executeComponent() {

        global $APPLICATION, $USER;

        try {
            
            $module_id = "travelsoft.reviews";

            if ($this->arParams["LINK_ELEMENT_ID"] <= 0) {
                throw new Exception("Не указан id привязки элемента");
            }

            $this->IncludeComponentTemplate();
        } catch (Exception $ex) {
            ShowError($ex->getMessage());
        }
    }

}
