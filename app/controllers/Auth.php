<?php

class Auth extends Controller {

    private $error = [];

    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function register() {
        $data = $_POST;
        //validate log pass / require + check login duplicate

        $user = $this->userModel->getUserByLogin($data['login']);
        if($user) {
            throw new CustomException('This login already exists',422);
        }
        if($this->userModel->create($data)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created', true, 201);
        } else {
            throw new CustomException('Error user register', 500);
        }

    }

    public function authorization() {
        $data = $_POST;
        //validate log pass / required

        $user = $this->userModel->getUserByLogin($data['login']);
        if(!$user) {
            throw new CustomException('User with login:'.$data['login'].' not found',404);
        }
        if(password_verify($data['password'],$user->password_hash)) {
            // generate token
        } else {
            throw new CustomException('Auth failed. Wrong login or password',403);
        }
    }
}