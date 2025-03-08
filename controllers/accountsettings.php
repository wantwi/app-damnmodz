<?php
require_once '../config.php';
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? null;


if(!empty($authHeader)){
  $session = check_session($authHeader);
  
  if($session['status'] === true){
    $id = $session['user'];
    $type = $session['type'];

$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if($data) {
        if(isset($data['editUser']) && !empty($data['userName'])){
            $update = $dbHandler-> updateData('users', 'name', $data['userName'], 'id', $id);
            if($update){
                $responseData = [
               "status"=> true,
               "message"=> "Name updated successfully",
               "data"=>[
                "name"=> $data['userName']
                ],
            ];
            }
        }
    }
    
      header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user = $dbHandler->selectData('users', 'id', $id);
        
        if(!empty($user)){
            $tfa = $user['google_2fa'] != null? true: false;
            
            $responseData = [
                "status"=> true,
                "google_fa"=> $tfa
            ];
        }
    
header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if($data) {
        if(isset($data['changePassword']) && !empty($data['userPassword']) && $data['userPassword'] === $data['userConPassword']){
                    // Hash the password
                $hashedPassword = password_hash($data['userPassword'], PASSWORD_DEFAULT);
                $update = $dbHandler-> updateData('users', 'password', $hashedPassword, 'email', $data['email']);
                if($update){
                    newLogs('system', "$data[email] changed password successfully",'success');
                     $responseData = [
               "status"=> true,
               "message"=> "Password updated successfully",
            ];
            }
        }else{
            newLogs('system', "$data[email] changed password unsuccessful",'error');
             $responseData = [
            "status"=> false,
            "message"=> "Passwords must match",
            ];
        }
    }
    
          header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}
}
}
