<?php
require_once '../config.php';
$responseData= [];
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? null;

if(!empty($authHeader)){
  $session = check_session($authHeader);
  
  if($session['status'] === true){
    $id = $session['user'];
    
    if($id === 20){
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            $user = $dbHandler->selectData('users', 'id', $id); 
            
            if(!empty($user)){
               $responseData=[
                "status" => true,
                "type"=> $user['type'],   
                ]; 
            }
        }
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT'){
             $data = json_decode(file_get_contents('php://input'), true);
             $type = isset($data) ? $data['type'] : '';
             
             if (!empty($type)) {
                $update = $dbHandler-> updateData('users', 'type', $type, 'id', $id);
                
                if($update){
                   $responseData=[
                    "status" => true,
                    "type"=> $type,
                    "message"=> 'Type changed successfully',
                    ];                    
                }
             }
            //
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($responseData);
    exit();
    }else{
    http_response_code(403);
    exit();
}
}else{
    http_response_code(403);
    exit();
}