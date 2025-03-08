<?php
require_once '../config.php';
$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? null;

if(!empty($authHeader)){
    $session = check_session($authHeader);
  
     if($session['status'] === true){
        $id = $session['user'];
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (!empty($id)) {
        $usersData = $dbHandler->selectData('users', 'id', $id);
    
        if(!empty($usersData) && $usersData['account_status'] != 'suspended'){
            $tfa = empty($usersData['google_2fa'])? false : true;
            $responseData = [
                "status"=>true,
                "tfa" => $tfa,
                ];
        }else{
                $responseData = [
                "status"=>false,
                "message" => "User not found."
                ];
        }
    }else{
                $responseData = [
                "status"=>false,
                "message" => "User not found."
                ];
    }
 
 header('Content-Type: application/json');
echo json_encode($responseData);
exit();   
}

    }
}else{
    echo '';
}