<?php

/**
 * Компонет добавления отзыва
 * 
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftReviewsStatistics extends CBitrixComponent {

    public function executeComponent() {

        try {

            if ($this->arParams["LINK_ELEMENT_ID"] <= 0) {
                throw new Exception("Не указан id элемента");
            }
            
            \Bitrix\Main\Loader::includeModule("travelsoft.reviews");
            $this->arResult["STATISTICS"] = (new travelsoft\reviews\Statistics($this->arParams["LINK_ELEMENT_ID"]))->get();

            Bitrix\Main\Page\Asset::getInstance()->addJs("/local/modules/travelsoft.reviews/plugins/raty/jquery.raty.js", true);
            
            $this->IncludeComponentTemplate();
        } catch (\Exception $ex) {
            ShowError($ex->getMessage());
        }
    }

}
