<?php
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();
$utils = $config->getUtils();


$responseData = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    if(isset($_GET['type'])){
        $type = $_GET['type'];
        if($type === 'all'){
        $logs =  $dbHandler->selectAllData('logs', "");    
        }else {
          $logs =  $dbHandler->selectAllData('logs', "WHERE type = '$type'");    
        }
        if(!empty($logs)){
            $responseData = [
            "status" => true,
            "logs" => $logs,
            ];
        }
        
    }
    
     header('Content-Type: application/json');
echo json_encode($responseData);
exit(); 
}