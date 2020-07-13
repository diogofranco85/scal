<?php

namespace Core;

class Input{

    private $request_body = [];

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

    public function put($key){
        global $_PUT;
        $_PUT = json_decode(file_get_contents('php://input'), true);
        if(array_key_exists($key, $_PUT)){
            return \html_entity_decode($_PUT[$key],ENT_QUOTES);
        }else{
            return false;
        }
    }

    public function delete($key){
        global $_DELETE;
        $_DELETE = json_decode(file_get_contents('php://input'), true);
        if(array_key_exists($key, $_DELETE)){
            return \html_entity_decode($_DELETE[$key],ENT_QUOTES);
        }else{
            return false;
        }
    }

    public function body($key){
        if(count($this->request_body) == 0 ){
            $this->request_body = json_decode(file_get_contents('php://input'), true);
        } 
        //__d($this->request_body);
        if(array_key_exists($key, $this->request_body)){
            return $this->request_body[$key];
        }

        return false;
        
    }

}