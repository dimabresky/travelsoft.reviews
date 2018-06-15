<?php

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ModuleManager,
    Bitrix\Main\Loader,
    Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

class travelsoft_reviews extends CModule {

    public $MODULE_ID = "travelsoft.reviews";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "N";
    public $namespaceFolder = "travelsoft";
    public $componentsList = array(
        "reviews",
        "reviews.add",
        "reviews.statistics",
        "reviews.list"
    );
    public $reviewsIblockId = null;
    public $reviewsIblockType = 'tsreviews';
    public $eventType = "TRAVELSOFT_REVIEWS";
    public $reviewsProperties = array();

    function __construct() {
        $arModuleVersion = array();
        $path_ = str_replace("\\", "/", __FILE__);
        $path = substr($path_, 0, strlen($path_) - strlen("/index.php"));
        include($path . "/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = Loc::getMessage("TRAVELSOFT_REVIEWS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("TRAVELSOFT_REVIEWS_MODULE_DESC");
        $this->PARTNER_NAME = "dimabresky";
        $this->PARTNER_URI = "https://github.com/dimabresky/";

        Loader::includeModule('iblock');
    }

    public function copyFiles() {

        foreach ($this->componentsList as $componentName) {
            CopyDirFiles(
                    $_SERVER["DOCUMENT_ROOT"] . "/local/modules/" . $this->MODULE_ID . "/install/components/" . $componentName, $_SERVER["DOCUMENT_ROOT"] . "/local/components/" . $this->namespaceFolder . "/" . $componentName, true, true
            );
        }
    }

    public function deleteFiles() {
        foreach ($this->componentsList as $componentName) {
            DeleteDirFilesEx("/local/components/" . $this->namespaceFolder . "/" . $componentName);
        }
        if (!glob($_SERVER["DOCUMENT_ROOT"] . "/local/components/" . $this->namespaceFolder . "/*")) {
            DeleteDirFilesEx("/local/components/" . $this->namespaceFolder);
        }
        return true;
    }

    public function DoInstall() {
        try {
            
            # регистрируем модуль
            ModuleManager::registerModule($this->MODULE_ID);
            
            # создание типа инфоблока
            $this->createReviewsIblockType();
            
            # создание инфоблока
            $this->createReviewsIblock();
            
            # создание свойств
            $this->createReviewsIblockproperties();
            
            # настройка отображения формы добавления отзыва
            $this->setEditReviewsIblockFormDisplay();
            
            # создание почтовых сообщений
            $this->createMailMessages();
            
            # копирование файлов
            $this->copyFiles();
            
            Option::set($this->MODULE_ID, "MAX_RATING_VALUE", 5);
            
        } catch (Exception $ex) {
            $GLOBALS["APPLICATION"]->ThrowException($ex->getMessage());
            $this->DoUninstall();
            return false;
        }

        return true;
    }

    public function DoUninstall() {

        # удаление типа инфоблока
        $this->deleteReviewsIblockType();
        
        # удаление почтовых сообщений
        $this->deleteMailMessages();
        
        # удаление файлов
        $this->deleteFiles();
        
        Option::delete($this->MODULE_ID, array('name' => 'MAX_RATING_VALUE'));
        
        ModuleManager::unRegisterModule($this->MODULE_ID);
        
        return true;
    }
    
    public function createMailMessages () {
        
        $et = new CEventType;
        if (!$et->Add(array(
            "LID"           => "ru",
            "EVENT_NAME"    => $this->eventType,
            "NAME"          => "Сообщения модуля отзывов",
            "DESCRIPTION"   => ""
            ))
        ) {
            throw new Exception($et->LAST_ERROR);
        }
        
        Option::set($this->MODULE_ID, "REVIEWS_MAIL_EVENT_TYPE", $this->eventType);
        
        $arFields = array(
            "ACTIVE" => "Y",
            "EVENT_NAME" => $this->eventType,
            "LID" => $this->getSiteId(),
            "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
            "EMAIL_TO" => "#EMAIL_TO#",
            "BODY_TYPE" => "html",
            "BCC" => '',
            "CC" => '',
            "REPLY_TO" => '',
            "IN_REPLY_TO" => '',
            "PRIORITY" => '',
            "FIELD1_NAME" => '',
            "FIELD1_VALUE" => '',
            "FIELD2_NAME" => '',
            "FIELD2_VALUE" => '',
            "SITE_TEMPLATE_ID" => '',
            "ADDITIONAL_FIELD" => array(),
            "LANGUAGE_ID" => ''
        );
        
        $arFields["MESSAGE"] = 'Добавлен новый <a href="https://#SERVER_NAME#/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.$this->reviewsIblockId.'&type='.$this->reviewsIblockType.'&ID=#ID#&lang=ru"">отзыв</a>';
        $arFields["SUBJECT"] = "Добавлен новый отзыв";
        
        $emess = new CEventMessage;
        if (!($id = $emess->Add($arFields))) {
            throw new Exception($emess->LAST_ERROR);
        }
        
        Option::set($this->MODULE_ID, "ADMIN_NOTIFICATION_MAIL_ID", $id);
        
        $arFields["MESSAGE"] = "Спасибо! Ваш отзыв принят. После проверки модератором он будет опубликован на сайте #SITE_NAME#";
        $arFields["SUBJECT"] = "Ваш отзыв принят";
        if (!($id = $emess->Add($arFields))) {
            throw new Exception($emess->LAST_ERROR);
        }
        
        Option::set($this->MODULE_ID, "USER_NOTIFICATION_MAIL_ID", $id);
        
        $arFields["MESSAGE"] = "Ваш отзыв опубликован на сайте #SITE_NAME#";
        $arFields["SUBJECT"] = "Ваш отзыв опубликован";
        if (!($id = $emess->Add($arFields))) {
            throw new Exception($emess->LAST_ERROR);
        }
        
        Option::set($this->MODULE_ID, "USER_NOTIFICATION2_MAIL_ID", $id);
    }
    
    public function deleteMailMessages () {
        
        $emess = new CEventMessage;
        $dbMess = $emess->GetList($by="site_id", $order="desc", array("TYPE_ID" => $this->eventType));
        while ($mess = $dbMess->Fetch()) {
            $emess->Delete($mess['ID']);
        }
        $et = new CEventType;
        $et->Delete($this->eventType);
        
        Option::delete($this->MODULE_ID, array('name' => 'REVIEWS_MAIL_EVENT_TYPE'));
        Option::delete($this->MODULE_ID, array('name' => 'ADMIN_NOTIFICATION_MAIL_ID'));
        Option::delete($this->MODULE_ID, array('name' => 'USER_NOTIFICATION_MAIL_ID'));
        Option::delete($this->MODULE_ID, array('name' => 'USER_NOTIFICATION2_MAIL_ID'));
    }
    
    public function createReviewsIblockType() {

        $arFields = Array(
            'ID' => 'tsreviews',
            'SECTIONS' => 'N',
            'IN_RSS' => 'N',
            'SORT' => 100,
            'LANG' => Array(
                'en' => Array(
                    'NAME' => 'TS Reviews',
                    'SECTION_NAME' => 'Sections',
                    'ELEMENT_NAME' => 'Review'
                ),
                'ru' => Array(
                    'NAME' => 'TS Отзывы',
                    'SECTION_NAME' => 'Разделы',
                    'ELEMENT_NAME' => 'Отзыв'
                )
            )
        );

        $obBlocktype = new CIBlockType;
        if (!$obBlocktype->Add($arFields)) {
            throw new Exception($obBlocktype->LAST_ERROR);
        }
        
        Option::set($this->MODULE_ID, "REVIEWS_IBLOCK_TYPE", $this->reviewsIblockType);
    }
    
    public function deleteReviewsIblockType () {
        
        CIBlockType::Delete($this->reviewsIblockType);
        CUserOptions::DeleteOptionsByName(
                "form", "form_element_" . Option::get($this->MODULE_ID, "REVIEWS_IBLOCK_ID"));
        Option::delete($this->MODULE_ID, array('name' => 'REVIEWS_IBLOCK_TYPE'));
        Option::delete($this->MODULE_ID, array('name' => 'REVIEWS_IBLOCK_ID'));
    }
    
    public function getSiteId () {
        
        static $arSites = array();
        
        if (!empty($arSites)) {
            return $arSites;
        }
        
        $dbSites = CSite::GetList($by = "sort", $order = "asc");
        
        while ($arSite = $dbSites->Fetch()) {
            $arSites[] = $arSite['ID'];
        }
        
        return $arSites;
    }
    
    public function createReviewsIblock() {
        
        $ib = new CIBlock();
        $arFields = Array(
            "CODE" => "TSREVIEWS",
            "NAME" => "Отзывы",
            "LIST_PAGE_URL" => "#SITE_DIR#/reviews",
            "DETAIL_PAGE_URL" => "#SITE_DIR#/reviews/#ELEMENT_CODE#",
            "IBLOCK_TYPE_ID" => $this->reviewsIblockType,
            "SITE_ID" => $this->getSiteId(),
            "GROUP_ID" => Array("2" => "R"),
            "ELEMENTS_NAME" => "Отзывы",
            "ELEMENT_NAME" => "Отзыв",
            "ELEMENT_ADD" => "Добавить отзыв",
            "ELEMENT_EDIT" => "Изменить отзыв",
            "ELEMENT_DELETE" => "Удалить отзыв"
        );
        
        if ( ! ($this->reviewsIblockId = $ib->Add($arFields)) ) {
            throw new Exception($ib->LAST_ERROR);
        }
        
        Option::set($this->MODULE_ID, "REVIEWS_IBLOCK_ID", $this->reviewsIblockId);
    }
    
    public function createReviewsIblockproperties () {
        
        $arProperties = array(
            array(
                "NAME" => "Пользователь",
                "ACTIVE" => "Y",
                "SORT" => 100,
                "CODE" => "USER_ID",
                "PROPERTY_TYPE" => "S",
                "USER_TYPE" => "UserID",
                "IBLOCK_ID" => $this->reviewsIblockId
            ),
            array(
                "NAME" => "Рейтинг",
                "ACTIVE" => "Y",
                "SORT" => 100,
                "CODE" => "RATING",
                "PROPERTY_TYPE" => "S",
                "IBLOCK_ID" => $this->reviewsIblockId
            ),
            array(
                "NAME" => "Изображения",
                "ACTIVE" => "Y",
                "SORT" => 100,
                "CODE" => "PICTURES",
                "MULTIPLE" => "Y",
                "PROPERTY_TYPE" => "F",
                "PROPERTY_FILE_TYPE" => "jpg,gif,bmp,png,jpeg",
                "IBLOCK_ID" => $this->reviewsIblockId
            ),
            array(
                "NAME" => "Привязка к элементу",
                "ACTIVE" => "Y",
                "SORT" => 100,
                "CODE" => "LINK_ELEMENT_ID",
                "PROPERTY_TYPE" => "E",
                "IBLOCK_ID" => $this->reviewsIblockId
            )
        );
        
        $ibp = new CIBlockProperty;
        foreach ($arProperties as $arProperty) {
            $this->reviewsProperties[$ibp->Add($arProperty)] = $arProperty['NAME'];
        }
    }
    
    public function setEditReviewsIblockFormDisplay () {
        
        $tabs = array(
            "Отзыв" => array(
                "NAME" => "Название",
                "ACTIVE" => "Активность"
            )
        );
        
        foreach ($this->reviewsProperties as $id => $name) {
            $tabs["Отзыв"]["PROPERTY_" . $id] = $name;
        }
        
        $tabs["Отзыв"]["DETAIL_TEXT"] = "Отзыв";
        
        $tabIndex = 0;
        $tabVals = array();
        foreach ($tabs as $tabTitle => $fields) {
            $tabCode = ($tabIndex == 0) ? 'edit' . ($tabIndex + 1) : '--edit' . ($tabIndex + 1);
            $tabVals[$tabIndex][] = $tabCode . '--#--' . $tabTitle . '--';
            foreach ($fields as $fieldKey => $fieldValue) {

                $fcode = $fieldKey;
                $ftitle = $fieldValue;
                
                $tabVals[$tabIndex][] = '--' . $fcode . '--#--' . $ftitle . '--';
            }
            $tabIndex++;
        }
        $opts = array();
        foreach ($tabVals as $fields) {
            $opts[] = implode(',', $fields);
        }
        $opts = implode(';', $opts) . ';--';
        $name = "form_element_" . $this->reviewsIblockId;
        $value = array(
            'tabs' => $opts
        );
        CUserOptions::SetOption("form", $name, $value, true);
        
    }

}
