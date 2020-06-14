<?php
/**
 * Created by PhpStorm.
 * User: diogofranco
 * Date: 23/12/18
 * Time: 23:25
 */

namespace Core\Html;


class TableCell extends Element{

    public function __construct($value)
    {
        parent::__construct('td');
        parent::add($value);
    }



}