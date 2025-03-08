<?php
require_once '../config.php';
$currentDatetime = date("Y-m-d H:i:s");

$query = "SELECT type,message, user_id, COUNT(*) AS unseen_count FROM notifications WHERE type = 'New Order' AND seen = 0 AND email_seen = 0 GROUP BY user_id;";
$stmt = $pdo->prepare($query);
$stmt->execute();
$unseenOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!empty($unseenOrders)){
    foreach ($unseenOrders as $orderData){
        $user = $dbHandler->selectData('users', 'id', $orderData['user_id']);
        if(!empty($user) && $user['account_status'] === 'active'){
               
           if($user['type'] === 'admin'){
                $url = 'admin/dashboard';
            }else{
                $url = 'dashboard';
            }
            
            $sendMail = orderNotification($user['email'], $user['name'], $orderData['unseen_count'], $url);
            
            
            // Update type
            if($sendMail){
                $dbHandler-> updateData('notifications', 'email_seen', '1', "email_seen = '0' AND user_id", $orderData['user_id']);
            } 
        }
        

    }
}

echo "Email notification sent!";
