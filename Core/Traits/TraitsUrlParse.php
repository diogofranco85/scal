<?php 

namespace Core\Traits;

trait TraitsUrlParse{

    public function parseUrl($url = null){

        return explode('/',rtrim($_SERVER['QUERY_STRING']),FILTER_SANITIZE_URL);

    }

}