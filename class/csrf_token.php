<?php

class CSRFToken
{
    private static $SECRET_KEY = 'P84ZTqV7GqTJTYeY7NYuTnFgR7JdewGKQnm7XsZsB3mVqH78UijdiseJ26jSZa7RdJfpew2KsV9XsX2H';

    // Generate a CSRF token and store it in the database
    public static function generateToken()
    {
        $pdo = DatabaseConnection::connect();

        $token = bin2hex(random_bytes(32));
        $hashedToken = hash_hmac('sha256', $token, self::$SECRET_KEY);

        // Insert the hashed token into the database with an expiry time
        $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry
        $insertQuery = "INSERT INTO CSRFToken (token, expiry) VALUES (:token, :expiry)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bindParam(':token', $hashedToken);
        $insertStmt->bindParam(':expiry', $expiry);
        $insertStmt->execute();

        return $token; // Return the raw token to the client
    }

    // Validate a CSRF token
    public static function validateToken($token)
    {
        $pdo = DatabaseConnection::connect();

        // Hash the raw token with the secret key
        $hashedToken = hash_hmac('sha256', $token, self::$SECRET_KEY);

        // Query the database for the hashed token
        $selectQuery = "SELECT * FROM CSRFToken WHERE token = :token AND expiry > NOW()";
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->bindParam(':token', $hashedToken);
        $selectStmt->execute();

        $result = $selectStmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            // Token is invalid or expired
            return false;
        }

        // Optionally, delete the token after validation to prevent reuse
        $deleteQuery = "DELETE FROM CSRFToken WHERE token = :token";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':token', $hashedToken);
        $deleteStmt->execute();

        return true; // Token is valid
    }
}
