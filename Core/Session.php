<?php

/**
 * Classe de controle de acesso variavel $_SESSION
 * @author Diogo Franco <diogo.franco@ubsvalorem.com.br>
 * @version 0.1
 * @copyright GPL © 2006, Valorem Agronegócios ltda.
 * @access public * @package rem
 * @subpackage core
 *
 */

namespace Core;

class Session{

    /**
     *Prefixo do nome da variavel a ser lida ou salva na sessão
     * @access protected
     * @name $prefix
     */
    
    protected static $prefix = '2ejo2(Qejd1-';

    public static function start($name = 'vlr_controller', $expire = 15){

        session_name($name);
        session_cache_expire($expire);
        session_start();
    }

    public function __set($key, $value){
        $_SESSION[self::$prefix . $key] = $value;

    }

    public function __get($key){
        if(isset($_SESSION[self::$prefix . $key])){
            return $_SESSION[self::$prefix . $key];
        }else{
            return false;
        }
         
    }

    public function unset($key){
        unset($_SESSION[$key]);
    }

    public function destroy(){
        session_destroy();
    }

    public function clear($key){
        unset($_SESSION[$this->prefix . $key]);
    }



}