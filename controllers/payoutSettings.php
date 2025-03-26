<?php
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();
$utils = $config->getUtils();


$responseData = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
        if($data) {
            if(isset($data['payoutNew'])){
               if(!empty($data['payoutName']) && !empty($data['payoutLabel'])){
                //Check for existing data
                $existingData = $dbHandler->existingData('payout_settings', 'payout_method', $data['payoutName']);
                if($existingData === 0){
                     $insertData = $dbHandler->insertData('payout_settings', 'payout_method', $data['payoutName']);
                     if($insertData){
                         $update = $dbHandler-> updateData('payout_settings', 'label_value', $data['payoutLabel'], 'payout_method', $data['payoutName']);
                         $responseData = [
                       "status"=> true,
                       "message"=> "Payout method added successfully"
                       ];
                     }
                }else{
                    $responseData = [
                       "status"=> false,
                       "message"=> "Payout method already exists"
                       ];
                }
               }else{
                   $responseData = [
                       "status"=> false,
                       "message"=> "input fields are required"
                       ];
               }
            }
            
             if(isset($data['editPayout'])){
                 $getPayout = $dbHandler->existingData('payout_settings', 'payout_method', $data['payoutName']);
                 if($getPayout===1){
                     $update=$dbHandler->updateData('payout_settings', 'payout_method', $data['payoutName'], 'id', $data['payoutId']);
                     $update=$dbHandler->updateData('payout_settings', 'label_value', $data['payoutLabel'], 'id', $data['payoutId']);
                     
                     if($update){
                          $responseData = [
                       "status"=> true,
                       "message"=> "Payout method updated successfully"
                       ];
                     }
                 }else{
                     $responseData = [
                       "status"=> false,
                       "message"=> "Payout method not found"
                       ]; 
                 }
             }
        }
    
header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    if(!isset($_GET['id'])){
          $payouts = $dbHandler->selectAllData('payout_settings', '');
    
    if(!empty($payouts)){
        $responseData = [
            "status"=> true,
            "message"=> "Payout methods retrieved successfully",
            "data"=> $payouts
            ];
    }  
    }
    
    if(isset($_GET['id'])){
        $payout = $dbHandler->selectData('payout_settings', 'id', $_GET['id']);
        if(!empty($payout)){
                    $responseData = [
            "status"=> true,
            "message"=> "Payout method retrieved Commission",
            "data"=> $payout
            ];
        }
    }

    
header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    
    if(isset($_GET['id'])){
        $del = $dbHandler->deleteData('payout_settings', 'id', $_GET['id']);
        
        if($del){
                    $responseData = [
            "status"=> true,
            "message"=> "Payout method deleted Commission",
            ];
        }else{
                    $responseData = [
            "status"=> false,
            "message"=> "Something went wrong",
            ];
        }
    }
    
    
  header('Content-Type: application/json');
echo json_encode($responseData);
exit();  
}