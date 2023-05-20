<?php

class Controller {
    public function __construct()
    {
        header("Content-Type:application/json");
    }
    protected function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

}