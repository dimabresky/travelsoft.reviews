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
  
            if ($this->arParams["LINK_ELEMENT_ID"] <= 0) {
                throw new Exception("Не указан id привязки элемента");
            }
                        
            \Bitrix\Main\Loader::includeModule("travelsoft.reviews");
            
            $element_id = intVal($this->arParams["LINK_ELEMENT_ID"]);
            
            $page = ($_REQUEST["PAGEN_1"] > 1 ? intVal($_REQUEST["PAGEN_1"]) : 1);
            
            $pageSize = $this->arParams["PAGE_SIZE"] > 0 ? $this->arParams["PAGE_SIZE"] : 10;
            
            $cacheId = "travelsoft_reviews" . $element_id . $pageSize . $page;
            
            $cache = new \travelsoft\reviews\Cache($cacheId);
            
            if (empty($this->arResult = $cache->get())) {
                
                $this->arResult = $cache->caching(function () use ($element_id, $pageSize, $page) {
                    
                    \Bitrix\Main\Loader::includeModule("travelsoft.reviews");
                    
                    $reviews = new travelsoft\reviews\Reviews;
                    
                    return $reviews->elementId($element_id)->pageSize($pageSize)
                            ->page($page)->get();
                    
                });
                
            }
            
            $this->IncludeComponentTemplate();
        } catch (Exception $ex) {
            ShowError($ex->getMessage());
        }
    }

}
