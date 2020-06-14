<?php
/**
 * Created by PhpStorm.
 * User: diogofranco
 * Date: 23/12/18
 * Time: 21:27
 */

namespace Core\Html;


class Element{

    private $name;
    private $properties;
    private $children;

    public function __construct($name){
        $this->name = $name;
    }

    public function __set($name, $value){
        $this->properties[$name] = $value;
    }

    public function __get($name){
        return $this->properties[$name];

    }

    public function add($child){
        $this->children[] = $child;
    }

    public function open(){
        echo "<{$this->name}";
        if($this->properties){
            foreach ($this->properties as $name => $value){
                echo " {$name}='{$value}'";
            }
        }
        echo '>';
    }

    public function show(){
        $this->open();
        echo "\n";
        if($this->children){
            foreach ($this->children as $child){
                if(is_object($child)){
                    $child->show();
                }else if(is_string($child) or is_numeric($child)){
                    echo $child;
                }
            }

            $this->close();
        }
    }

    public function close(){
        echo "</{$this->name}>\n";
    }

}