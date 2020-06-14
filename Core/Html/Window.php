<?php

namespace Core\Html;


class Window{

    private
        $content,
        $title,
        $action;
    static private $counter;

    public function __construct($title){

        self::$counter ++;
        $this->title = $title;

    }

    public function add($content){
        $this->content = $content;
    }

    public function show(){
        $id = 'window'.self::$counter;

        $panel = new Element('div');
        $panel->class = 'panel panel-default';
        $panel->id = $id;

        $heading = new Element('div');
        $heading->class = 'panel-heading';
        $heading->add($this->title);

        $title = new Element('div');
        $title->add($this->title);

        $body = new Element('div');
        $body->class = 'panel-body';
        $body->add($this->content);

        $panel->add($heading);
        $panel->add($body);

        if( is_array($this->action)){
            $footer = new Element('div');
            $footer->class = 'panel-footer';

            foreach($this->action as $key){

                $footer->add($key);

            }

            $panel->add($footer);
        }


        $panel->show();

    }

    public function addAction($label, $act, $icon){

        $this->action[$label] = new Element('button');
        $this->action[$label]->class = 'btn btn-sm btn-success btn-flat';
        $this->action[$label]->onclick = sprintf("alert(\"%s\");",$act);

        $i = new Element('i');
        $i->class= $icon;
        $i->add('');

        $this->action[$label]->add($i);
        $this->action[$label]->add($label);


    }


}