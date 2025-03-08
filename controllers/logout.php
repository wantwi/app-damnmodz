<?php
require_once '../config.php';
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

    $responseData = [
    "status"=>true,
    ];
    
header('Content-Type: application/json');
echo json_encode($responseData);
exit();

