<?php

final class JwtHelper {

    private function __construct()
    {
    }

    public static function generate_jwt($headers, $payload, $secret = JWT_SECRET)
    {
        $headers_encoded = self::base64url_encode(json_encode($headers));

        $payload_encoded = self::base64url_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = self::base64url_encode($signature);

        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";

        return $jwt;
    }

    private static function base64url_encode($str)
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    public static function is_jwt_valid($jwt, $secret = JWT_SECRET)
    {
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        $expiration = json_decode($payload)->exp;
        $is_token_expired = ($expiration - time()) < 0;

        $base64_url_header = self::base64url_encode($header);
        $base64_url_payload = self::base64url_encode($payload);
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
        $base64_url_signature = self::base64url_encode($signature);

        $is_signature_valid = ($base64_url_signature === $signature_provided);

        if ($is_token_expired || !$is_signature_valid) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public static function getUsername()
    {
        $headers = getallheaders();
        $token = substr($headers[REQUEST_TOKEN_HEADER],7);
        $tokenParts = explode('.', $token);
        $payload = json_decode(base64_decode($tokenParts[1]), true);
        return isset($payload['sub']) ? $payload['sub'] : '';
    }

    public static function getJti()
    {
        $headers = getallheaders();
        $token = substr($headers[REQUEST_TOKEN_HEADER],7);
        $tokenParts = explode('.', $token);
        $payload = json_decode(base64_decode($tokenParts[1]), true);
        return $payload['jti'];
    }
}