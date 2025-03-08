<?php

function newLogs($type, $description,$status)

{
    $dbHandler = new DatabaseHandler();
    $requestModel = new Request();
    
    
    $ip = $requestModel->getIpAddress();
    $isValidIpAddress = $requestModel->isValidIpAddress($ip);
    
    $geoLocationData = $requestModel->getLocation($ip);
    
    
    $id = $dbHandler->insertTheData('logs', 'type', $type);
    
    $country_code = isset($geoLocationData['country_code']) ? $geoLocationData['country_code'] : null;
    $ip = isset($geoLocationData['ip']) ? $geoLocationData['ip'] : null;
    
    if($id){
        $dbHandler->updateData('logs', 'description', $description, 'id', $id);
        $dbHandler->updateData('logs', 'status', $status, 'id', $id);
         $dbHandler->updateData('logs', 'country_code', $country_code, 'id', $id);
          $dbHandler->updateData('logs', 'ip', $ip, 'id', $id);
    }
    
    return $id;
}