<?php
    namespace Core;

    class application {

        private $get_set = array();
        private static $config = array();
        private $route;

        public function __construct()
        {
            $whoops = new \Whoops\Run;
            $whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
            
            date_default_timezone_set('America/Sao_Paulo');
            require_once( ROOT.DS.'App'.DS.'Config'.DS.'Routes.php');
        }

        public static function setConfig($key, $value){
            self::$config[$key] = $value;
        }

        public static function getConfig($key){
            return self::$config[$key];
        }

        public function __set($key, $value){
            $this->get_set[$key] = $value;
        }

        public function __get($key){
            return $this->get_set[$key];
        }

    }