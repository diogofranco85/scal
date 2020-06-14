<?php

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail extends PHPMailer{

    public function __construct(bool $exceptions = true){
        parent::__construct($exceptions);
    }

}