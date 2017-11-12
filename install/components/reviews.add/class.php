<?php

/**
 * Компонет добавления отзыва
 * 
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftReviewsAdd extends CBitrixComponent {
    
    public function executeComponent() {
    
        global $USER;
        
        $module_id = "travelsoft.reviews";
        
        $this->arResult['ERRORS'] = array();
        
        if (check_bitrix_sessid() && $_POST["add_review"]) {
            
            if (!$USER->isAuthorized()) {
                
                if (strlen($_POST['confirm_password']) > 0) {
                    
                    $captchaWord = '';
                    if (\Bitrix\Main\Config\Option::get("main", "captcha_registration") === 'Y') {
                        $captchaWord = $_POST['captcha_word'];
                    }
                    
                    $result = $USER->Register(
                                $_POST['email'], 
                                '',
                                '', 
                                $_POST['password'], 
                                $_POST['confirm_password'],
                                $captchaWord
                            );
                    
                    if ($result['TYPE'] === 'OK') {
                        
                        if (\Bitrix\Main\Config\Option::get("main", "new_user_registration_email_confirmation") === 'Y') {
                            $this->arResult["ERRORS"]["need_register_conf"] = $result['MESSAGE'];
                        }
                        
                    } else {
                        
                        $this->arResult["ERRORS"]["register_fail"] = $result['MESSAGE'];
                        
                    }
                } else {
                    
                    $result = $USER->Login($_POST['email'], $_POST['password'], "Y");
                    
                    if ($result !== true) {
                        $this->arResult["ERRORS"]["auth_fail"] = $result['MESSAGE'];
                    }
                }
            }
            
            $review = trim(strip_tags($_POST['review']));
                
            if (strlen($review) == 0) {

                $this->arResult['ERRORS']['empty_review'] = '';

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
                    
                    $rating = (int)trim(strip_tags($_POST['rating']));
                    if ($rating > 5) {
                        $rating = 5;
                    }
                }
                
                $images = array();
                if ($this->arParams['SHOW_IMAGES_FIELD'] === 'Y') {
                    
                    $images = $_FILES['images'];
                }
                
                if (empty($this->arResult['ERRORS'])) {
                    
                    $el = new CIBLockElemet;
                    
                    $result = $el->Add(array(
                        "NAME" => "Review_" . date('d.m.Y H:s:i'),
                        "ACTIVE" => $this->arParams['NEED_PREMODERATION'] === 'Y' ? 'Y' : 'N',
                        "DETAIL_TEXT" => $review,
                        "CODE" => "review_" . time(),
                        "PROPERTY_VALUES" => array(
                            "USER_ID" => $USER->GetID(),
                            "USER_NAME" => $USER->GetFullName(),
                            "USER_EMAIL" => $USER->GetEmail(),
                            "RATING" => $rating,
                            "ADVANTAGES" => $advantages,
                            "LIMITATIONS" => $limitations,
                            "PICTURES" => $images,
                            "LINK_ELEMENT_ID" => (int)$this->arParams['LINK_ELEMENT_ID']
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
                    
                    $this->arResult['ERRORS']['review_add_fail'] = $el->LAST_ERROR;
                }
            }
            
        }
        
        $this->IncludeComponentTemplate();
    }
}