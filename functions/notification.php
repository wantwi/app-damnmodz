<?php

function notifications($type, $message, $user, $url)

{
    $dbHandler = new DatabaseHandler();
    
    
    $id = $dbHandler->insertTheData('notifications', 'user_id', $user);
    
    if($id){
        $dbHandler->updateData('notifications', 'type', $type, 'id', $id);
        $dbHandler->updateData('notifications', 'message', $message, 'id', $id);
        $dbHandler->updateData('notifications', 'url', $url, 'id', $id);
    }
    
    return $id;
}