<?php

namespace App\Src;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email{

    private $email;
    private $html;

    public function __construct($fromEmail, $fromName = ''){

        $this->email = new PHPMailer(true);
        $this->email->CharSet        = 'UTF-8';
        $this->email->SMTPDebug      = 0;
        $this->email->isSMTP();
        $this->email->Host           = getenv('MAIL_HOST');
        $this->email->SMTPAuth       = true;
        $this->email->Username       = getenv('MAIL_USER');
        $this->email->Password       = getenv('MAIL_PASS');
        $this->email->SMTPSecure     = 'ssl';
        $this->email->SMTPOptions    = array ( 
            ' ssl ' => array (
                'confirm_peer' => false,
                'confirm_peer_name' => false,
                'allow_self_signed' => true
                )
        );
        $this->email->Port = getenv('MAIL_PORT');   
        $this->email->setFrom($fromEmail, $fromName);
        $this->email->isHTML(true); 
    }

    public function getTemplate($template){
        $dir = dirname(__FILE__);
        $file = $dir."/../../App/View/email/{$template}.html";
        if(!file_exists($file)){
            throw new Exception( json_encode([ 'status' => 500, 'message' => 'Arquivo de template de e-mail não existe']));
            exit();
        }
        $this->html = file_get_contents($dir."/../../App/View/email/{$template}.html");
    }

    public function setBody($html){
        $this->html = $html;
    }

    public function addAddress($toEmail){
        $this->email->addAddress($toEmail);       
    }
    
    public function subject($text){
        $this->email->Subject = $text;
    }

    public function send(){
        $this->email->Body = $this->html;
        return $this->email->send();
    }

}