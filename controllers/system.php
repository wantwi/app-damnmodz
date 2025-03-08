<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    if(isset($_GET['url'])){
        $wc_store = $dbHandler->selectData('app_system', 'system', 'wc_store');
        
        if(!empty($wc_store)){
            $responseData=[
            "status"=>true,
            "url"=> $wc_store['value']
            ];
        }
    }
    
    if(isset($_GET['email'])){
        $smtp = $dbHandler->selectData('app_system', 'system', 'smtp');
        $smtp_username = $dbHandler->selectData('app_system', 'system', 'smtp_username');
        $email_notification = $dbHandler->selectData('app_system', 'system', 'email_notification');
        
        $responseData=[
            "status"=>true,
            "smtp"=> $smtp['value'],
            "smtp_username"=> $smtp_username['value'],
            "email_notification"=> $email_notification['value'],
        ];
        
    }
header('Content-Type: application/json');
echo json_encode($responseData);
exit();   
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
    
    if($data){
        if (isset($data['apiUpdate']) && !empty($data['wc_consumer_key']) && !empty($data['wc_secrete_key']) && !empty($data['wc_store'])) {
            if ($data['wc_consumer_key'] != 'ck_0f8e78fb6fd032a1babc0fb3436cf01d2694a789c') {
                $update =$dbHandler->updateData('app_system', 'value', $data['wc_consumer_key'], 'system', 'wc_consumer_key');
            }
            
            if ($data['wc_secrete_key'] != 'cs_e5c2a8962dc5bbb2bf9853089907bd0ebd9d20c6f') {
                $update =$dbHandler->updateData('app_system', 'value', $data['wc_secrete_key'], 'system', 'wc_secrete_key');
            }

            $update =$dbHandler->updateData('app_system', 'value', $data['wc_store'], 'system', 'wc_store');
            
            if ($update) {
                $responseData=[
                "status"=>true,
                "message"=> 'Data updated successfully.'
                ];
            }
        }
        
        if (isset($data['emailSettings'])){
            if(!empty($data['smtp']) && !empty($data['username']) && !empty($data['password'])){
                $update =$dbHandler->updateData('app_system', 'value', $data['smtp'], 'system', 'smtp');
                $update =$dbHandler->updateData('app_system', 'value', $data['username'], 'system', 'smtp_username');
                $update =$dbHandler->updateData('app_system', 'value', $data['password'], 'system', 'smtp_password');
            }
            
            if (!empty($data['emailNots'])) {
                $email = $data['emailNots'];
            } else {
                $email = null;
            }
            
            
            $update =$dbHandler->updateData('app_system', 'value', $email, 'system', 'email_notification');
            
            if ($update) {
                $responseData=[
                "status"=>true,
                "message"=> 'Email data updated successfully.'
                ];
            }
        }
    }
    
header('Content-Type: application/json');
echo json_encode($responseData);
exit();   
}