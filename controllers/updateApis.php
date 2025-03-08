<?php
require_once '../config.php';
$currentDatetime = date("Y-m-d H:i:s");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
        if($data) 
    {
        if(!empty($data['wc_consumer_key']) && !empty($data['wc_secrete_key'])){
            $update =$dbHandler->updateData('app_system', 'value', $data['wc_consumer_key'], 'system', 'wc_consumer_key');
            $update = $dbHandler->updateData('app_system', 'value', $data['wc_secrete_key'], 'system', 'wc_secrete_key');
            
            if($update){
                $responseData = ["status"=>true, "message"=> "Api updated successfully"];
            }
        }
    }  
    
    
    header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}