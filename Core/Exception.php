<?php

namespace Core;

use Symfony\Component\HttpFoundation\Response;

class Exception {

        public function __construct($exception){

            $message = $exception->getMessage();
            $trace = $exception->getTraceAsString();

            $messageError = "
            <strong>Houve um erro no sistema</strong>
            <p style='background-color: #ddd; padding: 10px; margin-top:4px; margin-bottom: 4px;'>{$message}</p>
            <code>{$trace}</code>
            ";

            $json = json_encode([
                'status' => 500, 
                'message' => nl2br($messageError)
            ]);
           
            $response = new Response();
            $response->setStatusCode(200);
            $response->setContent($json);
            $response->setCharset('UTF-8');
            $response->headers->set('Content-Type:','application/json');
            $response->send();
            exit();        
        }



}