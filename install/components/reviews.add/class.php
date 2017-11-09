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
                            "PICTURES" => $images
                        )
                    ));
                    
                    if ($result) {
                        
                        
                        
                        LocalRedirect($APPLICATION->GetCurPageParam("", array(), false));
                    }
                    
                    $this->arResult['ERRORS']['review_add_fail'] = $el->LAST_ERROR;
                }
            }
            
        }
    }
}