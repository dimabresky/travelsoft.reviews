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
                                $_POST['confirm_password']
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
                
                
            }
            
        }
    }
}