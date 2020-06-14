<?php

namespace Core\Log;

class LoggerTXT extends Logger
{
  public function write($message){
    $time = date('d/m/Y H:i:s');
    $text = "{$time} :: {$message}";
    $handler = fopen("{$this->filename}",'a');
    fwrite($handler, $text);
    fclose($handler);
  }
}