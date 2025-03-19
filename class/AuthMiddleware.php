<?php

class AuthMiddleware
{
    private static $authUrl = 'http://auth-damnmodz.test:8080/api/user';
    private static $clientId = '3';
    private static $clientSecret = '';

    public static function validateToken()
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            http_response_code(401);
            return json_encode(['error' => 'Unauthorized - No token provided.']);
        }

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = str_replace('Bearer ', '', $authHeader);

        $options = [
            'http' => [
                'header' => "Authorization: Bearer $token\r\n" .
                    "Accept: application/json\r\n",
                'method' => 'GET',
            ],
        ];

        $context = stream_context_create($options);
        $result = @file_get_contents(self::$authUrl, false, $context);

        if ($result === FALSE) {
            http_response_code(401);
            return json_encode(['error' => 'Unauthorized - Invalid token.']);
        }

        return json_decode($result, true); // Return token data
    }
}



//class AuthMiddleware
//{
//    private static $authUrl = 'http://auth-damnmodz.test:8080/oauth/token';
//    private static $clientId = '3';
//    private static $clientSecret = '';
//
//    public static function validateToken()
//    {
//        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
//            http_response_code(401);
//            die(json_encode(['error' => 'Unauthorized - No token provided.']));
//        }
//
//        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
//        $token = str_replace('Bearer ', '', $authHeader);
//
//        $data = [
//            'grant_type' => 'check_token',
//            'client_id' => self::$clientId,
//            'client_secret' => self::$clientSecret,
//            'token' => $token,
//        ];
//
//        $options = [
//            'http' => [
//                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
//                'method' => 'POST',
//                'content' => http_build_query($data),
//            ],
//        ];
//
//        $context = stream_context_create($options);
//        $result = file_get_contents(self::$authUrl, false, $context);
//
//        if ($result === FALSE) {
//            http_response_code(401);
//            die(json_encode(['error' => 'Unauthorized - Invalid token.']));
//        }
//
//        return json_decode($result, true);
//    }
//}
