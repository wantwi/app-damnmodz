<?php
class ResponseSigner
{
    private static $SECRET_KEY = 'Hgudyrudf8559825823429rfdkjdsnJBJKDNSFSFHKJSDFNLSKGLM#jKHR49(0FJKDHS558SDFMNSLDFKJDFJDFSDFSEs'; // Use a secure, random key

    public static function signResponse($data)
    {
        // Encode the data as JSON (ensure a consistent format for hashing)
        $responseData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Generate the HMAC signature
        $signature = hash_hmac('sha256', $responseData, self::$SECRET_KEY);

        // Return the response with the signature
        return [
            'data' => $data,
            'signature' => $signature,
        ];
    }

    public static function verifySignature($data, $signature)
    {
        // Recompute the signature
        $computedSignature = hash_hmac('sha256', json_encode($data), self::$SECRET_KEY);

        // Compare signatures
        return hash_equals($computedSignature, $signature);
    }
}

// Example usage:
//$data = ['message' => 'Success', 'userId' => 123];
//$response = ResponseSigner::signResponse($data);

//echo json_encode($response);
