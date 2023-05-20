<?php

class Items extends Controller
{

    private $error=[];

    public function __construct()
    {
        parent::__construct();
        $this->itemsModel = $this->model('Item');
    }


    public function get($id)
    {
        //validate id...

         $item = $this->itemsModel->getItemById($id);
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

    public function create()
    {
        $data = $_POST;
        if($this->validate($data)) {
            if ($this->itemsModel->addItem($data)) {
                echo "ok";
                // 201 created
            } else {
                // error handle
            }
        } else {
            // validation error response
        }

    }

    public function update()
    {

    }

    public function delete($id)
    {
        //validate id
        $item = $this->itemsModel->getItemById($id);
        if($item){
            if ($this->itemsModel->deleteItem($id)) {
                // 204
                echo "ok";
            } else {
                //error delete
            }
        } else {
            // error not found
        }

    }

    private function validate() {
        return true;

    }

}
