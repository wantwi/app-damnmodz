<?php
require_once '../config.php';
$currentDatetime = date("Y-m-d H:i:s");
 $users = $dbHandler->selectAllData('users', "WHERE type ='user' And notification_message = 'New order'");
 if(!empty($users)){
     foreach ($users as $user){
        $sendEmail = newOrder($user['email'], $user['name']);
        
        if($sendEmail){
            $dbHandler-> updateData('users', 'notification_message', null, 'id', $user['id']);
            echo 'Cron Email sent at '. $currentDatetime . '</br>';
            newLogs('cron', "Email notification sent for new order",'success');
        }
     }
 }
 
 // for admin
 $admins = $dbHandler->selectAllData('users', "WHERE type ='admin' And notification_message = 'New order'");
 $email_notification = $dbHandler->selectData('app_system', 'system', 'email_notification');
if($email_notification['value'] != null && !empty($admins)){
     foreach ($admins as $admin){
        $sendEmail = newOrder($email_notification['value'], 'Admin');
        
        if($sendEmail){
            $dbHandler-> updateData('users', 'notification_message', null, 'id', $admin['id']);
            echo 'Cron Email sent at '. $currentDatetime . '</br>';
            newLogs('cron', "Email notification sent for new order",'success');
        }
     }
}