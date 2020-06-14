<?php
/**
 * Created by PhpStorm.
 * User: diogofranco
 * Date: 24/12/18
 * Time: 00:30
 */

namespace Core\Html;


class Import{

    private $src;
    //private static $counter;

    public function __construct($type = 'js', $src){

        //self::$counter ++;
        $this->src[] = array('type' => $type, 'src' => $src);

    }

    public function show(){

        foreach ($this->src as $key => $value){

           if($value['type'] == 'js'){
                echo $this->getJS($value['src']);
           }else{
               echo $this->getCSS($value['src']);
           }

        }

    }


    private function getJS($src){
        return "<script type='text/javascript' src='{$src}'></script>\n";
    }

    private function getCSS($src){
        return "<link href=\"{$src}\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />\n";
    }

}