<?php

session_start();

function validateToken() {

    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        http_response_code(401);
        die(json_encode(['error' => 'Unauthorized - No token provided.']));
    }


    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    $token = str_replace('Bearer ', '', $authHeader);

    // Validate the token with Laravel Passport
    $tokenUrl = 'http://auth-damnmodz.test:8080/oauth/token';

    $data = [
        'grant_type' => 'check_token',
        'client_id' => '3', // Your client ID
        'client_secret' => '', // Your client secret
        'token' => $token,
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($tokenUrl, false, $context);

    if ($result === FALSE) {
        http_response_code(401);
        die(json_encode(['error' => 'Unauthorized - Invalid token.']));
    }

    $response = json_decode($result, true);

    return $response;
}
?>

function refreshToken($refreshToken) {
    $tokenUrl = 'http://your-laravel-app.com/oauth/token';

    $data = [
        'grant_type' => 'refresh_token',
        'client_id' => '2',
        'client_secret' => '',
        'refresh_token' => $refreshToken,
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($tokenUrl, false, $context);

    if ($result === FALSE) {
        http_response_code(401);
        die(json_encode(['error' => 'Unauthorized - Token refresh failed.']));
    }

    $response = json_decode($result, true);

    // Save the new access token
    $_SESSION['token'] = $response['access_token'];
    return $response['access_token'];
}