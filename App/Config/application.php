<?php

define('ENCODE_KEY','Ag35susUE&sie29sjsn@*Ej93d');
define('ENVIRONMENT','DESENVOLVIMENTO');


$protocol = ($_SERVER['SERVER_PORT'] == 80) ? 'http'  : 'https';
$server = $_SERVER['SERVER_NAME'];
$directory = 'scal';

define('SITE_URL', "{$protocol}://{$server}/{$directory}");