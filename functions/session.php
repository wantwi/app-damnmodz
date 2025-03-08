<?php
require __DIR__. '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
//session_start();

function check_session($authHeader){
    $key = "juhsuUSSASG698sdfsjk42DHADJASKDD5asdhjfdfjhDSNMDFSFSF";
    
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        //print_r($decoded);
        
        if(!empty($decoded)){
            
            
            
            
            if(isset($decoded->user_id)){
                $userId = $decoded->user_id; // Extract the user_id
                $type = $decoded->type;
             $response = [
                "status"=> true,
                "user"=> $userId,
                "user_email" => null,
                "info" => $decoded,
                "type" => $type
                ];   
            }
            
            if(isset($decoded->order_number)){
                $userEmail = $decoded->user_email;
                $userId = $decoded->order_number;
             $response = [
                "status"=> true,
                "user"=> $userId,
                "user_email" => $userEmail,
                "info" => $decoded
                ];   
            }
            
            return $response;
            exit();
        }else{
            return false;
            exit();
        }
    } catch (Exception $e) {
        // Handle token errors
        //http_response_code(401);
        return false;
        exit();
    }    
        
    }
    
    

}

function verify_token($token){
    $key = "juhsuUSSASG698sdfsjk42DHADJASKDD5asdhjfdfjhDSNMDFSFSF";
    
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        //print_r($decoded);
        
        if(!empty($decoded)){
            $userId = $decoded->user_id; // Extract the user_id
            $response = [
                "status"=> true,
                "user"=> $userId,
                "info" => $decoded
                ];
            return $response;
            exit();
        }else{
            return false;
            exit();
        }
    } catch (Exception $e) {
        // Handle token errors
        //http_response_code(401);
        return false;
        exit();
    }
}

function active_user($userId){
     $dbHandler = new DatabaseHandler();
     
     $user = $dbHandler->selectData('users', 'id', $userId);
     
     if($user['account_status']==='active'){
         return true;
     }else{
         return false;
     }
}

function verify_key($id, $key){
    $dbHandler = new DatabaseHandler();
    
    $users = $dbHandler->selectData('users', 'id', $id);
    
    if(!empty($users)){
        // Verify password
        if (password_verify($key, $users['user_key'])) {
            return true;
        } else {
            return false;
        }
    }else{
        return false;
    }
}


?>