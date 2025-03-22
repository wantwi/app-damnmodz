
<?php
require __DIR__. '../../vendor/autoload.php';

class AuthMiddleware
{
    private static $cachedUser = null;

    public static function validateToken()
    {
        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            self::unauthorizedResponse('No token provided.');
        }

        $authHeader = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $authHeader);

        if (empty($token)) {
            self::unauthorizedResponse('Token is empty.');
        }

        $user = self::getUserFromT($token);

        if (!$user) {
            self::unauthorizedResponse('Invalid or expired token.');
        }

        self::$cachedUser = $user;
        return self::$cachedUser;
    }

    private static function getUserFromT($token)
    {
        try {
            $parts = explode('.', $token);

            if (count($parts) !== 3) {
                throw new Exception("Invalid token format");
            }

            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

            if (!$payload) {
                throw new Exception("Invalid token payload");
            }

            return $payload;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token: ' . $e->getMessage()]);
            exit();
        }
    }

    private static function unauthorizedResponse($message)
    {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized - ' . $message]);
        exit();
    }
}
