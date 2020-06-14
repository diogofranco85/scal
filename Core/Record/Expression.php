<?php

namespace Core\Record;

abstract class Expression{

    const AND_OPERATOR = 'AND';
    const OR_OPERATOR = 'OR';
    
    abstract public function dump();
}