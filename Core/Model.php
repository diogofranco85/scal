<?php

namespace Core;

use \Core\Database\Transaction;
use \Core\Log\LoggerTXT;
use \Core\Log\LoggerHTML;
use \Core\Record\Expression;


class Model{

    private $db;
    private $object = array();
    private $columns = '*';
    private $limit = 1000;
    private $offset = 0;
    private $where = array();
    private $sql;
    private $countReg;
    private $joins = array();
    private $group = '';
    private $orderby = '';

    public $timestamps = false;
    public $tablename;
    public $primarykey = 'id';
    public $key_update;
    public $softDeleteKey = 'ativo';

    public function __construct(String $schema, String $key = 'id'){

        $this->tablename = $schema;
        $this->primarykey = $key;

        if(!$this->db){
            $this->db = Transaction::getInstance();
        }
    }

    public function getNull()
    {
        return \PDO::PARAM_NULL;
    }

    public function fillable($value){

        if(is_array($value)){
            $columns = '';
            foreach ($value as $key => $item) {
                foreach($item as $k => $v) {
                    $key == 'void' ? $columns .= " {$v}, " : $columns .= " $key.$v, ";
                }
            }

            $columns = rtrim($columns, ', ');
            $this->columns = $columns;
        }else {
            $this->columns = $value;
        }
    }

    public function select(String $columns = '*'){
        $this->fillable($columns);
    }

    public function load(Int $id)
    {
        $sql = "SELECT {$this->columns} FROM {$this->tablename} WHERE {$this->primarykey} = {$id}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function find(int $id, $columns = '*')
    {
        $class = get_called_class();
        $table = (new $class())->tablename;
        $key = (new $class())->primarykey;
        $sql = "SELECT {$columns} FROM {$table} WHERE {$key} = {$id}";
        
        try{
            $db = Transaction::getInstance();
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $fo = $stmt->fetchObject(get_called_class());
            $fo->key_update = $id;
            return $fo;
        }catch(\PDOException $e){
            $log = new LoggerHTML('database.html');
            $log->write('Model:' . get_called_class() .' <br> Erro ao buscar registro por ID: '.$e->getMessage() . "<br> <b>SQL:</b><span style='color: red'>{$sql}</span>");
            echo $e->getMessage();
            exit();
        }
        
    }

    public function toArray(){
        return $this->object;
    }

    public static function all($conditions = '', int $limit = 0, int $offset = 0)
    {
        $class = get_called_class();
        $table = (new $class())->tablename;
        $key = (new $class())->primarykey;
        $sql = "SELECT * FROM {$table}";
        $sql .= ($conditions != '') ? " WHERE {$conditions}" : '';
        $sql .= ($limit != '') ? " LIMIT {$limit}" : '';
        $sql .= ($offset != '') ? " OFFSET {$offset}" : '';
        
        try{
            $db = Transaction::getInstance();
            $stmt = $db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            $log = new LoggerHTML('database.html');
            $log->write('Model:' . get_called_class() .' <br> Erro ao buscar registro por ID: '.$e->getMessage() . "<br> <b>SQL:</b><span style='color: red'>{$sql}</span>");
            echo $e->getMessage();
            exit();
        }
        
    }

    public function where(String $key, String $conditions, String $value, $operator = Expression::AND_OPERATOR)
    {
        if(is_numeric($value)){
            $value = $value;
        }else{
            $value = "'{$value}'";
        };

        $this->where[] = "{$key} {$conditions} {$value} {$operator}";
    }

    public function group(String $columns){
        $this->group = "GROUP BY $columns";
    }

    public function order(String $columns = null){
        $col = ($columns == null) ? $this->primarykey : $columns;
        $this->orderby = "ORDER BY $col";
    }

    public function get($columns = null){

        $where = '';

        if(count($this->where) > 0){
            if(count($this->where) == 1){
                $where = 'WHERE ' . $this->where[0];
                $where = str_replace(' AND','',$where);
                $where = str_replace(' OR','',$where);
            }else{
                $where = 'WHERE ';
                foreach( $this->where as $w){
                    $where .= " $w ";
                }
            }

            $where = rtrim($where, 'AND ');
            $where = rtrim($where, 'OR ');
        }

        $innerJoin = '';
        if(count($this->joins) > 0)
        {
            foreach($this->joins as $ij){
                $innerJoin .= $ij;
            }
        }

        $offset = $this->offset;
        $limit = $this->limit;

        if($columns == null){
            $columns = $this->columns;
        }

        $group = $this->group;
        $order = $this->orderby;

        $sql = "SELECT {$columns} FROM ".$this->tablename." {$innerJoin} {$where} {$group} {$order} LIMIT {$offset},{$limit} ";
        $this->sql = $sql;

        try{
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $this->countReg = $stmt->rowCount();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->clear();
        return $result;

        }catch(\PDOException $e){
            $log = new LoggerHTML('database.html');
            $log->write('Model:' . get_called_class() .' <br> Erro ao consultar: '.$e->getMessage() . "<br> <b>SQL:</b><span style='color: red'>{$this->sql}</span>");
            echo $e->getMessage();
            exit();
        }
            
    }

    public function create()
    {
        $col = '';
        $val = '';

        if($this->timestamps == true){
            $this->object['created_at'] = date('Y-m-d h:m:s');
        }

        foreach($this->object as $columns => $value)
        {
            $col .= " {$columns},";
            if(is_string($value)){
                $val .= " '{$value}',";
            }else{
                $val .= " {$value},";
            }
        }

        $col = rtrim($col, ',');
        $val = rtrim($val,',');
        $sql = "INSERT INTO {$this->tablename} ($col) VALUES ($val)";
        $this->sql = $sql;
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $e){
            $log = new LoggerHTML('database.html');
            $log->write('Model:' . get_called_class() .' <br> Erro ao gravar dados: '.$e->getMessage() . "<br> <b>SQL:</b><span style='color: red'>{$this->sql}</span>");
            echo $e->getMessage();
            exit();
        }
        if($stmt->rowCount() == 1){
            return true;
        }

        return false;

    }

    public function update()
    {
        $set = '';

        if($this->timestamps == true){
            $this->object['updated_at'] = date('Y-m-d h:m:s');
        }

        foreach($this->object as $key => $value)
        {
            if($key != $this->primarykey){
                if(is_numeric($value)){
                    $set .= " {$key} = $value,";
                }else{
                    $set .= " {$key} = '$value',";
                }
            }
            
        }

        $set = rtrim($set, ',');

    $sql = "UPDATE {$this->tablename} SET {$set} WHERE {$this->primarykey} = {$this->key_update}";
        $this->sql = $sql;
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $e){
            $log = new LoggerHTML('database.html');
            $log->write('Model:' . get_called_class() .' <br> Erro ao atualizar dados: '.$e->getMessage(). "<br> <b>SQL:</b><span style='color: red'>{$this->sql}</span>");
            echo $e->getMessage();
            exit();   
        }

        if($stmt->rowCount() == 1){
            return true;
        }
        return false;
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->tablename} WHERE {$this->primarykey} = {$this->object[$this->primarykey]} ";
        $this->sql = $sql;

        try{
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        }catch(\PDOException $e){
            $log = new LoggerHTML('database.html');
            $log->write('Model:' . get_called_class() .' <br> Erro ao excluir: '.$e->getMessage(). "<br> <b>SQL:</b><span style='color: red'>{$this->sql}</span>");
            echo $e->getMessage();
            exit();
        }
    }

    public function softDelete(){
        $class = get_called_class();
        $class = $class::find($this->id);
        $class->ativo = 'N';
        $class->update();
    }

    public function innerJoin($table, $idtable, $idRerefence, $table_reference = null)
    {   

        if(is_array($table)){
            $tablename = $table['table'];
            $as = ' AS '.$table['as'];
            $alias = $table['as'];
        }else{
            $as = '';
            $tablename = $table;
            $alias = $table;
        };

       

        if(is_null($table_reference))
        {
            $table_reference = $this->tablename;
        }

        $this->joins[] = " INNER JOIN {$tablename} {$as} ON {$alias}.{$idtable} = ".$table_reference.".{$idRerefence} ";
    }

    public function rightJoin($table, $idtable, $idRerefence, $table_reference = null)
    {

        
        if(is_array($table)){
            $tablename = $table['table'];
            $as = ' AS '.$table['as'];
            $alias = $table['as'];
        }else{
            $as = '';
            $tablename = $table;
            $alias = $table;
        };


        if(is_null($table_reference))
        {
            $table_reference = $this->tablename;
        }

        $this->joins[] = " RIGHT JOIN {$tablename} {$as} ON {$alias}.{$idtable} = ".$table_reference.".{$idRerefence} ";
    }

    public function leftJoin($table, $idtable, $idRerefence, $table_reference = null)
    {
        
        if(is_array($table)){
            $tablename = $table['table'];
            $as = ' AS '.$table['as'];
            $alias = $table['as'];
        }else{
            $as = '';
            $tablename = $table;
            $alias = $table;
        };


        if(is_null($table_reference))
        {
            $table_reference = $this->tablename;
        }

        $this->joins[] = " LEFT JOIN {$tablename} {$as} ON {$alias}.{$idtable} = ".$table_reference.".{$idRerefence} ";
    }


    public function count(){

        __d($this->object);

    }

    public static function countAll()
    {
        $class_called = get_called_class();
        $class = new $class_called;

        $sql = "SELECT count({$class->primarykey}) AS 'qde' FROM {$class->tablename}";
        $db = \Core\Database\Transaction::getInstance();
        $rs = $db->query($sql);
        $row = $rs->fetch(\PDO::FETCH_ASSOC);
        return $row['qde'];
    }

    public function query(String $sql ){
        return $this->getSQL($sql, false);
    }

    public function getSQL(String $SQL, Bool $toObject = false){

        try{
            $query = $this->db->prepare($SQL);
            $result = $query->execute();
            return $this->object = ($toObject == false) ? $query->fetchAll(\PDO::FETCH_ASSOC) : $query->fetchObject(get_class($this));
        }catch(\PDOException $e){
            $log = new LoggerHTML('database.html');
            $log->write('Model:' . get_called_class() .' <br> Erro ao executar SQL: '.$e->getMessage(). "<br> <b>SQL:</b><span style='color: red'>{$this->sql}</span>");
            echo $e->getMessage();
            exit();
        }
    }

    public function logSQL(String $control, String $act, $param = null){
            $tabela =  'log_sql';
            $idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0 ;
            $sql = \htmlspecialchars($this->sql);
            $ip = $_SERVER['REMOTE_ADDR'];

            $cmdSQL = "INSERT INTO {$tabela} (codigo, idusuario, controller, method, ip) VALUES (\"{$sql}\" , \"{$idusuario}\", \"{$control}\", \"{$act}\", \"{$ip}\");";
            
            $db = \Core\Database\Transaction::getInstance();
            $query = $db->prepare($cmdSQL);
            $query->execute();
    }

    public function lastID(){
        $sql = ("select max({$this->primarykey}) as {$this->primarykey} from {$this->tablename}");
        $row = $this->db->execute($sql);
        return $row[0][$this->primarykey];
    }

    public function __clone(){
        unset($this->object[$this->primarykey]);
    }

    public function __get(String $key){

        if(method_exists($this, 'get_'.$key)){
            return call_user_func(array($this, 'get_'.$key));
        }else{
            return $this->object[$key];
        }

    }

    public function __set(String $key, $value){

        if(method_exists($this, 'set_'.$key)){
            call_user_func(array($this, 'set_'.key), $value);
        }else{
            $this->object[$key] = $value;
        }

    }

    private function clear(){
        $this->where = array();
        $this->joins = array();
    }


}