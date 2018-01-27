<?php

/**
 * Компонет списка отзывов
 * 
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftReviews extends CBitrixComponent {

    public function executeComponent() {

        global $APPLICATION, $USER;

        try {
  
            if ($this->arParams["LINK_ELEMENT_ID"] <= 0) {
                throw new Exception("Не указан id привязки элемента");
            }
            
            $this->IncludeComponentTemplate();
        } catch (Exception $ex) {
            ShowError($ex->getMessage());
        }
    }

}
