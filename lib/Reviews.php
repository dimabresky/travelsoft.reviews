<?php

namespace travelsoft\reviews;

/**
 * Класс для работы с отзывами
 *
 * @author dimabresky
 * @copyright (c) 2018, travelsoft
 */
class Reviews {

    protected $_filter = array("ACTIVE" => "Y");
    protected $_pageSize = null;
    protected $_stars = null;
    protected $_order = null;
    protected $_page = null;

    public function __construct() {

        $this->_filter["IBLOCK_ID"] = \Bitrix\Main\Config\Option::get("travelsoft.reviews", "REVIEWS_IBLOCK_ID");
        $this->_setDefault();
    }

    public function getCount() {

        $arFilter = $this->_filter;

        if ($this->_stars !== null) {
            $arFilter["PROPERTY_RATING_VALUE"] = $this->_stars > 0 ? $this->_stars : false;
        }

        return \CIBlockElement::GetList(array(), $arFilter, array(), false);
    }

    public function get() {

        $arFilter = $this->_filter;

        if ($this->_stars !== null) {
            $arFilter["PROPERTY_RATING_VALUE"] = $this->_stars > 0 ? $this->_stars : false;
        }

        $arNav["nPageSize"] = $this->_pageSize;
        $arNav["iNumPage"] = 1;
        if ($this->_page > 1) {
            $arNav["iNumPage"] = $this->_page;
        }

        $arResult["dbList"] = \CIBlockElement::GetList($this->order, $arFilter, false, $arNav);

        $arUsers = array();
        while ($element = $arResult["dbList"]->GetNextElement()) {

            $arFields = $element->GetFields();
            $arProperties = $element->GetProperties();

            $arUser = array(
                "ID" => null,
                "PERSONAL_PHOTO" => null,
                "AVATAR" => "/local/modules/travelsoft.reviews/img/no_user_photo.png",
                "EMAIL" => null
            );
            if ($arProperties["USER_ID"]["VALUE"] > 0) {
                if (isset($arUsers[$arProperties["USER_ID"]["VALUE"]])) {
                  $arUser = $arUsers[$arProperties["USER_ID"]["VALUE"]];
                } else {
                    $arr = \CUser::GetByID($arProperties["USER_ID"]["VALUE"])->Fetch();
                    if ($arr["ID"] > 0) {
                        $arUser["ID"] = $arr["ID"];
                        $arUser["EMAIL"] = $arr["EMAIL"];
                        if ($arr["PERSONAL_PHOTO"] > 0) {
                            $arUser["PERSONAL_PHOTO"] = $arr["PERSONAL_PHOTO"];
                            $img = \CFile::ResizeImageGet($arr["PERSONAL_PHOTO"], array('width' => 40, 'height' => 40), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            if ($img) {
                                $arUser["AVATAR"] = $img["src"];
                            }
                        }
                        $arUsers[$arUser["ID"]] = $arUser;
                    }
                }
            }
            
            $arPictures = array();
            if (!empty($arProperties["PICTURES"]["VALUE"])) {
                
                foreach ($arProperties["PICTURES"]["VALUE"] as $id) {
                    
                    $img = \CFile::ResizeImageGet($id, array('width' => 600, 'height' => 500), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
                    $arPictures[] = array("ID" => $id, "SRC" => $img["src"]);
                }
            }
            
            $arResult["ITEMS"][] = array(
                "ID" => $arFields["ID"],
                "DATE_CREATE" => $arFields["DATE_CREATE"],
                "RATING" => intVal($arProperties["RATING"]["VALUE"]),
                "REVIEW_TEXT" => $arFields["DETAIL_TEXT"],
                "~REVIEW_TEXT" => $arFields["~DETAIL_TEXT"],
                "USER" => $arUser,
                "PICTURES" => $arPictures,
                "USER_NAME" => $arProperties["USER_NAME"]["VALUE"],
                "USER_COMPANY" => $arProperties["USER_COMPANY"]["VALUE"],
                "USER_EMAIL" => $arProperties["USER_COMPANY"]["VALUE"]
            );
        }
        
        return $arResult;
    }

    public function elementId(int $element_id) {
        $this->_filter["PROPERTY_LINK_ELEMENT_ID"] = $element_id;
        return $this;
    }

    public function stars(int $stars) {
        $this->_stars = $stars;
        return $this;
    }

    public function page(int $page) {
        $this->_page = $page;
        return $this;
    }

    public function pageSize(int $pageSize) {
        $this->_pageSize = $pageSize;
        return $this;
    }

    public function reset() {
        $this->_setDefault();
        return $this;
    }

    public function order(string $by, string $direct) {
        $this->_order = array($by => $direct);
    }

    protected function _setDefault() {
        $this->_page = 1;
        $this->_pageSize = 10;
        $this->_order = array("ID", "DESC");
        $this->_stars = null;
    }

}
