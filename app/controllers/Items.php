<?php

class Items extends Controller
{
    public function get($id) {
        echo json_encode([
           'data' => [
               'id' => $id,
               'name' => 'Test',
               'phone' => '123456788',
               'key' => 'key_value'
           ]
        ]);
    }

    public function store() {


    }

    public function update() {

    }

    public function delete ($id) {

    }

}