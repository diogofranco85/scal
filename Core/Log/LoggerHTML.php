<?php

namespace Core\Log;

class LoggerHTML extends Logger{

  public function write($message){

    $time = date('d/m/Y H:i:s');
    $text = "<p> \n";
    $text .= "<b>$time</b> <br> \n";
    $text .= "<i>$message</i> <br> \n";
    $text .= "</p> <hr> \n";

    $handler = fopen("{$this->filename}",'a');
    fwrite($handler, $text);
    fclose($handler);

  }

}