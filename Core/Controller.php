<?php

namespace Core;

use \Core\Template\Template;
use \Core\Session;
use \Core\Input;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Controller{

    protected $request;
    protected $response;
    protected $import = [];
    private   $userdata = [];

    public function __construct(){

        if( getenv('ENVIRONMENT') == 'development'){
            error_reporting(E_ALL);
            ini_set('display_errors', true);
        }else{
            error_reporting(E_ERROR | E_WARNING);
            ini_set('display_errors', false);
        }

        $this->userData('url_base', getenv('APP_URL'));
       
        $this->load("Template",'view', 'Core\Template');
        $this->load('Input','input');
        $this->load('Session','session');
        $this->load('Encrypt','encrypt');

        $this->request = Request::createFromGlobals();
        $this->response = new Response();      
    }

    public function load($classe, $name = null, $pack = 'Core')
    {

        $ns = "\\{$pack}\\{$classe}";
        $name = $name == null ? $classe : strtolower($name);

       try{
            $this->import[$name] = new $ns();
       }catch(Exception $e){
           echo $e->getMessage();
       }

    }

    public function loadHelper($helper){

        $file = strtolower($helper);
        $filename = ROOT.DS.'core'.DS.'helpers'.DS."helper_{$file}.php";
        if(file_exists($filename)){
            require_once($filename);
        }else{
            Throw(new \Exception('Arquivo de helper não localizado: '.$filename));
        }

    }

    public function redirect($uri, $external = false)
    {
        if($external){
            $uri = $this->userdata['url_base'].'/'.$uri;
        }

        $redir = new RedirectResponse($uri);
        $redir->send();
        
    }

    public function jsonResponse(array $json, $status = 200, $charset = 'UTF-8')
    {
        $json = json_encode($json);
        $this->response->setStatusCode($status);
        $this->response->setContent($json);
        $this->response->setCharset($charset);
        $this->response->headers->set('Content-Type:','application/json; text/plain');
        $this->response->send();
    }

    public function render($file, $status = 200, $charset = 'UTF-8', $headers = ['Content-type:' => 'text/html'])
    {
        $html = $this->view->render($file, $this->userdata);
        $this->response->setStatusCode($status);
        $this->response->setCharset($charset);
        foreach($headers as $key => $value){
            $this->response->headers->set($key, $value);
        }
        $this->response->setContent($html);
        $this->response->send();
    }

    public function userData($key, $value)
    {
        $this->userdata[$key] = $value;
    }

    public function getUserData($key)
    {
        return key_exists($key, $this->userdata) ? $this->userdata[$key] : false;
    }

    public function getURL(String $route)
    {
        return $this->getUserData('url_base') . "/{$route}";
    }

    public function html($file)
    {
        return $this->view->render($file, $this->userdata);
    }


    public function __set($key, $value)
    {
         $this->import[$key] = $value;
    }

    
    public function __get($key)
    {
        $key = strtolower($key);
        return $this->import[$key];
    }

}
