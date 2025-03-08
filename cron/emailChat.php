<?php
require_once '../config.php';
$currentDatetime = date("Y-m-d H:i:s");

$query = "SELECT sender,name, COUNT(*) AS unseen_count FROM chat WHERE seen = 1 AND email_seen = 1 GROUP BY sender;";
$stmt = $pdo->prepare($query);
$stmt->execute();
$unseenMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Loop through each sender and send a notification email
foreach ($unseenMessages as $messageData) {
    $email = $messageData['sender'];
    $name = $messageData['name'];
    $unseenCount = $messageData['unseen_count'];

    // Send a single email to the sender with the total unseen count
    $sent =chatNotification($email, $name, $unseenCount);
    
    // Update type
    if($sent){
        $dbHandler-> updateData('chat', 'email_seen', '0', "email_seen = '1' AND sender", "$email");
    }
}

echo "Daily unseen messages notifications sent!";
