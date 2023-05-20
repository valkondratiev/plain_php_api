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

    public function authorization()
    {
        $data = $_POST;
        //validate log pass / required

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

            $jwt = $this->generate_jwt($headers, $payload);

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

    // JWT

    private function generate_jwt($headers, $payload, $secret = 'secret')
    {
        $headers_encoded = $this->base64url_encode(json_encode($headers));

        $payload_encoded = $this->base64url_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = $this->base64url_encode($signature);

        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";

        return $jwt;
    }

    private function base64url_encode($str)
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    private function is_jwt_valid($jwt, $secret = 'secret')
    {
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        $expiration = json_decode($payload)->exp;
        $is_token_expired = ($expiration - time()) < 0;

        $base64_url_header = $this->base64url_encode($header);
        $base64_url_payload = $this->base64url_encode($payload);
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
        $base64_url_signature = $this->base64url_encode($signature);

        $is_signature_valid = ($base64_url_signature === $signature_provided);

        if ($is_token_expired || !$is_signature_valid) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    ///////
}