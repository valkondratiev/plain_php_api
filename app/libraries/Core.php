<?php

class Core {
    private $currentController = 'Items';
    private $currentMethod = 'get';
    private $params = [];


    public function __construct()
    {
        $url = $this->getUrl();
        print_r($url);
    }


    private function getUrl() {
        if(isset($_SERVER['REQUEST_URI'])) {
            $url = trim($_SERVER['REQUEST_URI'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/',$url);
            return $url;
        }
    }


}