<?php

define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
require_once ROOT.DS."vendor/autoload.php";
require_once ROOT.DS.'functions.php';

//$whoops = new \Whoops\Run;
//$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
//$whoops->register();

$SentryConfig = [
    'dsn' => 'https://fbc8ef36cea84e3e810e7d0ac51c68f2@o376185.ingest.sentry.io/5196784'
];

Sentry\init($SentryConfig);

use Core\Session;
Session::start();
$app = new Core\Application();




