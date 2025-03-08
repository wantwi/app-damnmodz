<?php
require_once '../config.php';
$currentDatetime = date("Y-m-d H:i:s");
    if (! function_exists('imap_open')) {
        echo "IMAP is not configured.";
        exit();
    } else {
// Using PHP IMAP to read emails
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'reply@damnmodz.com';
$password = 'aqbt kpjq qmqe ahlz';

// Connect to the mailbox
$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());

// Search for unread emails
$emails = imap_search($inbox, 'UNSEEN');

if($inbox){
    if ($emails) {
        foreach ($emails as $email_number) {
            $structure = imap_fetchstructure($inbox, $email_number);
            $overview = imap_fetch_overview($inbox, $email_number, 0);
            $message = imap_fetchbody($inbox, $email_number, 1.1);
            $subject = $overview[0]->subject;
            $date = date("Y-m-d H:i:s", strtotime($overview[0]->date));
            
            // Extract the sender's email address
            $header = imap_headerinfo($inbox, $email_number);
            $fromAddress = $header->from[0]->mailbox . "@" . $header->from[0]->host;
            $fromName = $header->from[0]->personal;
    
            // Extract unique token from subject
            if (preg_match('/Message Token: (\w+)/', $subject, $matches)) {
                $token = $matches[1];
                $filename = null;
                $insert = true;
                
                            
                if (empty($message)) {
                    $message = imap_fetchbody($inbox, $email_number, 1); // if no plain text, fetch HTML body
                }
                
                 // Loop through email parts to find attachments/images
                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); $i++) {
                        $part = $structure->parts[$i];
    
                        // Check if the part is an image
                        if ($part->ifdisposition && (strtolower($part->disposition) == 'attachment' || strtolower($part->disposition) == 'inline')) {
                            if (strtolower($part->subtype) == 'jpeg' || strtolower($part->subtype) == 'png') {
                                // It's an image attachment (jpeg or png)
                                $filename = $part->dparameters[0]->value;
                                $imageData = imap_fetchbody($inbox, $email_number, $i+1);
                                if ($part->encoding == 3) { // BASE64 encoding
                                    $imageData = base64_decode($imageData);
                                } elseif ($part->encoding == 4) { // QUOTED-PRINTABLE encoding
                                    $imageData = quoted_printable_decode($imageData);
                                }
                                
                                $folder = $_SERVER['DOCUMENT_ROOT']. '/images';
    
                                // Save the image to your server
                                file_put_contents("$folder/$filename", $imageData);
                                $insert = true;
                            }
                        }
                    }
                }
                
                
                
                // Selete all nessecarry info
                $product = $dbHandler->selectData('products', 'hash_id', $token);
                
                if($product && !empty($product['supplier_id'])){
                    $supplier = $dbHandler->selectData('users', 'id', $product['supplier_id']);
                    
                    if($supplier['email'] === $fromAddress){
                        $type = 'user';
                        $name = $supplier['name'];
                    }else{
                        $type = 'client';
                        $name = $product['customer_name'];
                    }
                    
                    $insert = true;
                }
    
                if($insert){
                    $seen = 1;
                    $insertQuery = "INSERT INTO chat (product_id, message, image, sender, name, type, seen, date) VALUES (:product_id, :message, :image, :sender, :name, :type, :seen, :date)";
                    $insertStmt = $pdo->prepare($insertQuery);
                    $insertStmt->bindParam(':product_id', $token);
                    $insertStmt->bindParam(':message', $message);
                    $insertStmt->bindParam(':image', $filename);
                    $insertStmt->bindParam(':sender', $fromAddress);
                    $insertStmt->bindParam(':name', $name);
                    $insertStmt->bindParam(':type', $type);
                    $insertStmt->bindParam(':seen', $seen);
                    $insertStmt->bindParam(':date', $date);  // Single dollar sign
                    $insert = $insertStmt->execute();
                    
                    if($type === 'user'){
                        $reciver = $product['customer_email'];
                        newChat($reciver, $message, $product['order_id'], $order['order_key'] );
                    }else{
                        
                        if(!empty($supplier['email'])){
                                $reciver = $supplier['email'];
                                $update = $dbHandler-> updateData('users', 'notification_message', 'New message', 'id', $product['supplier_id']);
                                $update = $dbHandler-> updateData('users', 'notification_time', $currentDatetime, 'id', $product['supplier_id']);
                                
                                if($supplier['type'] == 'admin'){
                                    $url = "admin/dashboard/order/$product[hash_id]";
                                }else{
                                    $url = "dashboard/order/$product[hash_id]";
                                }
                                
                                newChats($supplier['email'], '', $product['order_id'], $url);
                        }
        
                    }
                    
                     newLogs('cron', "Email chat reply successful",'success');   
                }else{
                 newLogs('cron', "Database insertion failed for email chat reply", 'error');  
                }
                
                // Save the reply to the database based on the token
                // (Query your database to find the chat using the token and store the message)
            }
        }
    }    
}else{
    newLogs('cron', "Failed to close IMAP connection: " . imap_last_error(), 'error');
}


imap_close($inbox);

}
