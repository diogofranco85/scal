<?php

namespace Core;

class Input{


    public function post($key, $value = NULL){
            if(is_null($value)){
                if(array_key_exists($key, $_POST)){
                    return \html_entity_decode($_POST[$key], ENT_QUOTES);
                }else{
                    return false;
                }
            }else{
                $_POST[$key] = \htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
    }

    public function get($key, $value = NULL){
        if(is_null($value)){
            if(array_key_exists($key, $_GET)){
                return \html_entity_decode($_GET[$key],ENT_QUOTES);
            }else{
                return false;
            }
        }else{
            $_GET[$key] = \htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
}

}