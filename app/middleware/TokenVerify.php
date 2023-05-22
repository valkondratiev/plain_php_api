<?php
require_once __DIR__.'/../models/User.php';

class TokenVerify {

    public static function checkAuth() {

        $headers = getallheaders();
        if(empty($headers[REQUEST_TOKEN_HEADER])) {
            throw new CustomException('Access denied', 403);
        }

        if(strpos($headers[REQUEST_TOKEN_HEADER],'Bearer ') !== 0) {
            throw new CustomException('Access denied. Wrong token format', 403);
        }

        $token = substr($headers[REQUEST_TOKEN_HEADER],7);

        if(empty($token)) {
            throw new CustomException('Access denied. Invalid token', 403);
        }

        if(JwtHelper::is_jwt_valid($token)) {
            $userModel = new User();
            $user = $userModel->checkJti(JwtHelper::getJti());
            if(!$user) {
                throw new CustomException('Access denied. Token valid but user generate newer', 403);
            }
            return true;
        } else {
            throw new CustomException('Access denied. Token verification failed', 403);
        }

    }


}