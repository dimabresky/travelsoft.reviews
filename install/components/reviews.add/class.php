<?php

/**
 * Компонет добавления отзыва
 * 
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftReviewsAdd extends CBitrixComponent {

    public function executeComponent() {

        global $APPLICATION, $USER;
        
        $module_id = "travelsoft.reviews";
        
        if ($this->arParams['SHOW_RATING_FIELD'] === 'Y') {
            
            Bitrix\Main\Page\Asset::getInstance()->addJs("/local/modules/$module_id/plugins/raty/jquery.raty.min.js", true);
        }
        
        $this->arResult['ERRORS'] = array();

        if (check_bitrix_sessid() && strlen($_POST["add_review"]) > 0) {

            if (!$USER->isAuthorized()) {

                    if (key_exists('confirm_password', $_POST)) {

                        if (\Bitrix\Main\Config\Option::get("main", "captcha_registration") === 'Y') {
                            
                            $result = $USER->Register(
                                    $_POST['email'], '', '', $_POST['password'], $_POST['confirm_password'], $_POST['email'], SITE_ID, $_POST['captcha_word'], $_POST['captcha_sid']
                            );
                            
                        } elseif ($this->checkCaptcha()) {

                            $result = $USER->Register(
                                    $_POST['email'], '', '', $_POST['password'], $_POST['confirm_password'], $_POST['email'], SITE_ID
                            );

                        }
                        
                        if (empty($this->arResult["ERRORS"])) {
                            
                            if ($result['TYPE'] === 'OK') {

                                if (\Bitrix\Main\Config\Option::get("main", "new_user_registration_email_confirmation") === 'Y') {
                                    $this->arResult["ERRORS"][] = "NEED_REGISTER_CONF";
                                }
                                
                            } else {
                                
                                $this->arResult["ERRORS"][] = "REGISTER_FAIL";
                                
                            }
                        }
                        
                    } elseif($this->checkCaptcha()) {

                        $result = $USER->Login($_POST['email'], $_POST['password'], "Y");

                        if ($result !== true) {
                            $this->arResult["ERRORS"][] = "AUTH_FAIL";
                        }
                    }
                
            }

            $review = trim(strip_tags($_POST['review']));

            if (strlen($review) == 0) {

                $this->arResult['ERRORS'][] = 'EMPTY_REVIEW';
            }

            if (empty($this->arResult['ERRORS'])) {

                $limitations = '';
                if ($this->arParams['SHOW_LIMITATIONS_FIELD'] === 'Y') {

                    $limitations = trim(strip_tags($_POST['limitations']));
                }

                $advantages = '';
                if ($this->arParams['SHOW_ADVANTAGES_FIELD'] === 'Y') {

                    $advantages = trim(strip_tags($_POST['advantages']));
                }

                $rating = 0;
                if ($this->arParams['SHOW_RATING_FIELD'] === 'Y') {
                    
                    $mrv = Bitrix\Main\Config\Option::get($module_id, "MAX_RATING_VALUE");
                    
                    $rating = (int) trim(strip_tags($_POST['rating']));
                    if ($rating > $mrv) {
                        $rating = $mrv;
                    }
                }

                $images = array();
                if ($this->arParams['SHOW_IMAGES_FIELD'] === 'Y') {

                    $images = $_FILES['images'];
                }

                if (empty($this->arResult['ERRORS'])) {
                    
                    Bitrix\Main\Loader::includeModule("iblock");
                    
                    $el = new CIBlockElement();

                    $result = $el->Add(array(
                        "IBLOCK_ID" => Bitrix\Main\Config\Option::get("travelsoft.reviews", "REVIEWS_IBLOCK_ID"),
                        "NAME" => "Review_" . date('d.m.Y H:s:i'),
                        "ACTIVE" => $this->arParams['NEED_PREMODERATION'] === 'Y' ? 'N' : 'Y',
                        "DETAIL_TEXT" => $review,
                        "CODE" => "review_" . $USER->GetID() . time(),
                        "PROPERTY_VALUES" => array(
                            "USER_ID" => $USER->GetID(),
                            "USER_NAME" => $USER->GetFullName(),
                            "USER_EMAIL" => $USER->GetEmail(),
                            "RATING" => $rating,
                            "ADVANTAGES" => $advantages,
                            "LIMITATIONS" => $limitations,
                            "PICTURES" => $images,
                            "LINK_ELEMENT_ID" => (int) $this->arParams['LINK_ELEMENT_ID']
                        )
                    ));

                    if ($result) {

                        Bitrix\Main\Mail\Event::send(array(
                            "EVENT_NAME" => \Bitrix\Main\Config\Option::get($module_id, 'REVIEWS_MAIL_EVENT_TYPE'),
                            "LID" => SITE_ID,
                            "MESSAGE_ID" => \Bitrix\Main\Config\Option::get($module_id, "ADMIN_NOTIFICATION_MAIL_ID"),
                            "C_FIELDS" => array(
                                "ID" => $result
                            )
                        ));

                        $_SESSION['__TRAVELSOFT']['REVIEWS_MESS_OK'] = "DEFAULT_OK_MESSAGE";
                        if ($this->arParams['NEED_PREMODERATION'] === 'Y') {

                            $_SESSION['__TRAVELSOFT']['REVIEWS_MESS_OK'] = "PREMODERATION_OK_MESSAGE";
                            Bitrix\Main\Mail\Event::send(array(
                                "EVENT_NAME" => \Bitrix\Main\Config\Option::get($module_id, 'REVIEWS_MAIL_EVENT_TYPE'),
                                "LID" => SITE_ID,
                                "MESSAGE_ID" => \Bitrix\Main\Config\Option::get($module_id, "USER_NOTIFICATION_MAIL_ID")
                            ));
                        } else {

                            Bitrix\Main\Mail\Event::send(array(
                                "EVENT_NAME" => \Bitrix\Main\Config\Option::get($module_id, 'REVIEWS_MAIL_EVENT_TYPE'),
                                "LID" => SITE_ID,
                                "MESSAGE_ID" => \Bitrix\Main\Config\Option::get($module_id, "USER_NOTIFICATION2_MAIL_ID")
                            ));
                        }

                        LocalRedirect($APPLICATION->GetCurPageParam("", array(), false));
                    }
                    
                    $this->arResult['ERRORS'][] = 'REVIEW_ADD_FAIL';
                }
            }
        }

        if (!$USER->isAuthorized()) {
            $this->arResult['CAPTCHA_CODE'] = $APPLICATION->CaptchaGetCode();
        }

        $this->IncludeComponentTemplate();
    }
    
    /**
     * @global CMain $APPLICATION
     * @return boolean
     */
    public function checkCaptcha () {
        
        global $APPLICATION;
        if (!$APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_sid"])) {
                                
            $this->arResult['ERRORS'][] = "CAPTCHA_FAIL";
            return false;
        }
        return true;
    }

}
