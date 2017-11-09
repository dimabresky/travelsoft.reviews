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
                }
            }
            
            if (empty($this->arResult['ERRORS'])) {
                
                $review = trim(strip_tags($_POST['review']));
                
                if (strlen($review) == 0) {
                    
                    $this->arResult['ERRORS']['empty_review'] = '';
                    
                }
                
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
            }
            
        }
    }
}