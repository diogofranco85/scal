<?php

namespace Core\Log;

abstract class Logger{

  public function __construct($filename)
  {
    $this->filename = ROOT.DS.'Log'.DS.$filename;
    //file_put_contents($this->filename,'');
  }

  abstract function write($message);

}