<?php
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();
$utils = $config->getUtils();

$currentDatetime = date("Y-m-d H:i:s");

// Post requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
    
    if($data){
         // New Supplier
        if(isset($data['newSupplier']) && !empty($data['name']) && !empty($data['email'])){
            //Check for existing user
            $existingUser = $dbHandler->existingData('users', 'email', $data['email']);
            
            if($existingUser[0]===0){
                    $password = $utils->getToken(16);
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    // Insert the new user into the database
                    $insertData = $dbHandler->insertData('users', 'email', $data['email']);
                    if($insertData){
                    $update = $dbHandler-> updateData('users', 'password', $hashedPassword, 'email', $data['email']);
                    $update = $dbHandler-> updateData('users', 'type', 'user', 'email', $data['email']);
                    $update = $dbHandler-> updateData('users', 'name', $data['name'], 'email', $data['email']);
                    
                        if ($update){
                            $selectUser = $dbHandler->selectData('users', 'email', $data['email']);
                            foreach ($data['category'] as $category){
                                $insertCats = $dbHandler->insertTwoData('supplier_categories', 'user_id', 'wc_category_id', $selectUser['id'], $category);
                            }
                            
                            $sendMail = sendWelcomeEmail($data['email'], $data['name'], $password);
                           if($sendMail){
                             $responseData = [
                                "status"=>true,
                                "message"=> "User added successfully"
                            ];  
                           }
                           
                            
                    }else{
                        $responseData = [
                            "status"=>false,
                            "message"=> "Something went wrong try again"
                        ];
                        
                    }
            }
        }else{
                $responseData = [
                    "status"=> false,
                    "message"=> "User with email: $data[email] already exist."
                    ];
            }
    }
    
    header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}

}
// Get requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
    
    if(isset($_GET['user'])){
            $selectQuery = "SELECT 
            users.id, users.name, users.email,users.created_at, users.account_status,
            SUM(CASE WHEN products.status = 'completed' THEN 1 ELSE 0 END) AS completedOrders,
            SUM(CASE WHEN products.status = 'ongoing' THEN 1 ELSE 0 END) AS ongoingOrders
        FROM users
        LEFT JOIN products ON users.id = products.supplier_id
        WHERE users.type = 'user' AND users.id = '$_GET[user]'
        GROUP BY users.id, users.name, users.email";
    $selectStmt = $pdo->prepare($selectQuery);
    $selectStmt->execute();
    $selectTable = $selectStmt->fetch(PDO::FETCH_ASSOC);

$responseData=[
    "status"=> true,
    "data"=> $selectTable,
    //"created"=> date("d M, Y", strtotime($selectTable[created_at]))
];
    }
    
    if(isset($_GET['view'])){
            $selectQuery = "SELECT 
            users.id, users.name, users.email, users.account_status,
            SUM(CASE WHEN products.status = 'completed' THEN 1 ELSE 0 END) AS completedOrders,
            SUM(CASE WHEN products.status = 'ongoing' THEN 1 ELSE 0 END) AS ongoingOrders
        FROM users
        LEFT JOIN products ON users.id = products.supplier_id
        WHERE users.type = 'user'
        GROUP BY users.id, users.name, users.email";
    $selectStmt = $pdo->prepare($selectQuery);
    $selectStmt->execute();
    $selectTable = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($selectTable)) {
$responseData=[
    "status"=> true,
    "data"=> $selectTable
];
}

    }    
    



if(isset($_GET['paymethod'])){
    $payout =$dbHandler->selectAllData('users_payout', "WHERE user_id = $_GET[paymethod]");
    
    if(!empty($payout)){
        $responseData=[
    "status"=> true,
    "data"=> $payout
        ];
    }
}


if(isset($_GET['select'])){
    $users =$dbHandler->selectAllData('users', "WHERE type = 'user'");
    
    if(!empty($users)){
        $responseData=[
    "status"=> true,
    "data"=> $users
        ];
    }
}

 
    
    
    header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}
// Put requests
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
    
        if($data){
            // User category update
            if(isset($data['editSupplier']) && !empty($data['id']) && !empty($data['category'])){
                $delete = $dbHandler->deleteData('supplier_categories', 'user_id', $data['id']);
                if ($delete) {
                    foreach ($data['category'] as $category){
                    $insertCats = $dbHandler->insertTwoData('supplier_categories', 'user_id', 'wc_category_id', $data['id'], $category);
                    }
                    if ($insertCats) {
                        $responseData = [
                            "status" => true,
                            "message"=> 'Category updated successfully'
                        ];
                    }
                }
            }
            
            // Password reset
            if(isset($data['resetPass']) && !empty($data['id'])){
                $user = $dbHandler->selectData('users', 'id', $data['id']);
                if ($user) {
                    $password = $utils->getToken(16);
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $update = $dbHandler-> updateData('users', 'password', $hashedPassword, 'email', $user['email']);
                    if($update){
                        adminPasswordReset($user['email'], $user['name'], $password);
                        $responseData = [
                            "status" => true,
                            "message"=> 'Password updated successfully'
                        ];                        
                    }
                } else {
                        $responseData = [
                            "status" => false,
                            "message"=> 'Something went wrong. Try again.'
                        ];
                }
                

            }
            

            
        
    }
    
        if(isset($_GET['suspend'])){
            // suspend user
            $user = $dbHandler->selectData('users', 'id', $_GET['suspend']);
            
            if(!empty($user)){
                
                if($user['account_status'] != 'suspended'){
                    $update = $dbHandler-> updateData('users', 'account_status', 'suspended', 'id', $user['id']);
                    $message = "You suspended $user[name]";
                }else{
                    $password = $utils->getToken(16);
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $update = $dbHandler-> updateData('users', 'password', $hashedPassword, 'id', $user['id']);
                    $update = $dbHandler-> updateData('users', 'account_status', 'pending', 'id', $user['id']);
                    $sendMail = sendWelcomeEmail($user['email'], $user['name'], $password);
                    
                    $message = "You unsuspend $user[name]";
                }
                
               
               
               if($update){
                    $responseData = [
                    "status"=>true,
                    "message"=> $message,
                    "account_status"=> $user ['account_status']
                ];                   
               }else{
                    $responseData = [
                    "status"=>false,
                    "message"=> "Something went wrong"
                ];                   
               }
            }
            
        }
    
    if(isset($_GET['verifySend'])){
   $user = $dbHandler->selectData('users', 'id', $_GET['verifySend']); 
   
   if (!empty($user)) {
        $password = $utils->getToken(16);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $update = $dbHandler-> updateData('users', 'password', $hashedPassword, 'id', $user['id']);
        
        if($update){
            $sendMail = sendWelcomeEmail($user['email'], $user['name'], $password);
            
            if($sendMail){
                $responseData = [
                "status"=>true,
                "message"=> "Verification email sent successfully"
            ];
            }else{
                $responseData = [
                "status"=>false,
                "message"=> "Something went wrong"
            ];                
            }
        }
       
   }
}
   
    
    
    header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}