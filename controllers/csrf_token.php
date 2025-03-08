<?php
require_once '../config.php';

$token = CSRFToken::generateToken();

$responseData = ["status"=>true, "token"=> $token];

header('Content-Type: application/json');
echo json_encode($responseData);
exit();