<?php

class Auth extends Controller {

    private $error = [];

    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function register() {
        $data = $_POST;
        if (!$this->validate($data)) {
            throw new ValidationException($this->error, 'Validation error', 400);
        }

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

    public function authorization()
    {
        $data = $_POST;
        if (!$this->validate($data)) {
            throw new ValidationException($this->error, 'Validation error', 400);
        }

        $user = $this->userModel->getUserByLogin($data['login']);
        if (!$user) {
            throw new CustomException('User with login:' . $data['login'] . ' not found', 404);
        }
        if (password_verify($data['password'], $user->password_hash)) {
            // generate token
            $headers = array('alg'=>'HS256','typ'=>'JWT');
            $jti = bin2hex(random_bytes(32));
            $exp = time() + TOKEN_EXPIRE_TIME;
            $payload = array('sub'=>$data['login'], 'jti'=>$jti, 'exp'=>$exp);

            $jwt = JwtHelper::generate_jwt($headers, $payload);

            if(!$this->userModel->updateTokenInfo($user->id, $jti, $exp)){
                throw new CustomException('Error authorization', 500);
            }

            $response = [
                'data' => [
                    'token' => $jwt,
                    'expire' => $exp
                ]
            ];
            echo json_encode($response);
        } else {
            throw new CustomException('Auth failed. Wrong login or password', 403);
        }
    }

    private function validate($data) {
        if(!isset($data['login'])) {
            $this->error[] = [
                'field' => 'login',
                'message' => 'Field login required'
            ];
        }
        if(!isset($data['password'])) {
            $this->error[] = [
                'field' => 'password',
                'message' => 'Field password required'
            ];
        }
        if (!preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/', $data['login'])) {
            $this->error[] = [
                'field' => 'login',
                'message' => 'Field login must start with letter, contain letters and numbers only, 6-32 characters'
            ];
        }
        if (!preg_match('/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $data['password'])) {
            $this->error[] = [
                'field' => 'password',
                'message' => 'Field password must be a minimum of 8 characters, contain at least 1 number, contain at least one uppercase character, contain at least one lowercase character'
            ];
        }
        if($this->error) {
            return false;
        }
        return true;
    }
}