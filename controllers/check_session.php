<?php
require_once '../config.php';
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
$key = "juhsuUSSASG698sdfsjk42DHADJASKDD5asdhjfdfjhDSNMDFSFSF";
    
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? null;

if(!empty($authHeader)){
    $session = check_session($authHeader);
    
    if($session['status'] === true){
        
        if(!empty($session['user'])){
          $active_user = active_user($session['user']);  
        }else if(isset($session['user_email'])){
          $active_user = true;  
        }
        
       
      if($active_user){
        $responseData = [
            "status" => true,
            "message" => 'authorized',
            ];  
      }else{
        $responseData = [
            "status" => false,
            "message" => 'Account was suspended',
            "info"=>$session
        ];          
      }
    }else{
        $responseData = [
            "status" => false,
            "message" => 'Your Session expired. Login to continue',
            "info"=>$session
        ]; 
    }    
}else{
        $responseData = [
            "status" => false,
            "message" => 'Your Session expired. Login to continue',
            "info"=>$session
        ];    
}

header('Content-Type: application/json');
echo json_encode($responseData);
exit();
    
}