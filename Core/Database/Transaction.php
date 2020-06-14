<?php

namespace Core\Database;

use \PDO;

class Transaction{

    private static $con = NULL;

    public function __construct(){

        throw new \Exception('Utilize o metodo "Transaction::open(conexao); ');

    }

    public static function open(String $file){
            $filename = ROOT.DS.'App'.DS.'Database'.DS.$file.'.ini';
            
            if(file_exists($filename)){
                $inifile = parse_ini_file($filename,false);
                $type = $inifile['type'];
                $user = $inifile['user'];
                $pass = $inifile['pass'];
                $host = $inifile['host'];
                $dbname = $inifile['dbname'];
                $port = $inifile['port'];

                switch($type){
                    case  "mysql" :
                    self::$con = new PDO("mysql:host={$host};dbname={$dbname}",$user, $pass,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                    self::$con->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
                    break;

                    case 'pgsql':
                    self::$con = new PDO("pgsql:dbname={$dbname};user={$user};password={$pass};host={$host}");
                    break;
                }

                self::$con->setAttribute(PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$con->beginTransaction();

            }else{
                throw new \Exception("arquivo de configuranção não existe");
                die();
            }
            
    }

    public static function close(){
            self::$con->commit();
            self::$con = NULL;
    }

    public static function getInstance(){
        if(!is_null(self::$con)){
            return self::$con;
        }else{
            throw new \Exception('<pre>Error: Não há conexão ao banco de dados ativa</pre');
        }
    }

    public static function rollback(){
        
        self::$con->rollback();
        self::$con = NULL;

    }

}