<?php

class Items extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->ItemsModel = $this->model('Item');
    }


    public function get($id)
    {
        //validate id...

         $item = $this->ItemsModel->getItemById($id);
         if($item) {
             $response = [
                 'data' => (array)$item
             ];
             echo json_encode($response);
         } else {
             echo 'not found'; die();
             // not found ....  add error handle
         }
    }

    public function store()
    {


    }

    public function update()
    {

    }

    public function delete($id)
    {

    }

}
