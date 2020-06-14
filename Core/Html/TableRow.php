<?php
/**
 * Created by PhpStorm.
 * User: diogofranco
 * Date: 23/12/18
 * Time: 23:24
 */

namespace Core\Html;


class TableRow extends Element{

    public function __construct()
    {
        parent::__construct('tr');
    }

    public function addCell($value){

        $cell = new TableCell($value);
        parent::add($cell);
        return $cell;

    }

}