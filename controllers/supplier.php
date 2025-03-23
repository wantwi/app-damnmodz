<?php
use config\Config;

require_once '../config/Config.php';

$config = Config::getInstance();
$pdo = $config->getPDO();
$dbHandler = $config->getDbHandler();
$authUser = $config->getAuthUser();
$utils = $config->getUtils();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
        if($data) 
    {
        // Validate CSRF TOKEN
        if (isset($data['login_process']) && CSRFToken::validateToken($data['token'])) {
        
            $email = isset($data['email']) ? $data['email'] : '';
            $password = isset($data['password']) ? $data['password'] : '';
            
            if(!empty($email) && !empty($password)){
                
                //Check for existing user
                $existingUser = $dbHandler->existingData('users', 'email', $email);
                
                if($existingUser[0]=== 0){
                        // Hash the password
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        // Insert the new user into the database
                        $insertData = $dbHandler->insertData('users', 'email', $email);
                        
                        if($insertData){
                            $update = $dbHandler-> updateData('users', 'password', $hashedPassword, 'email', $email);
                            $update = $dbHandler-> updateData('users', 'type', 'admin', 'email', $email);
                            $update = $dbHandler-> updateData('users', 'name', 'DamnModz', 'email', $email);
                            
                            if ($update){
                                $responseData = [
                                    "status"=>true,
                                    "message"=> "User added successfully"
                                ];   
                            }else{
                            $responseData = [
                                "status"=>false,
                                "message"=> "Database error: $stmt->error"
                            ];
                                
                            }
                        }


                }else{
                    
                    $responseData = [
                    "status"=>false,
                    "message"=> "Supplier with email: $email already exists."
                    ];
                    
                }
            }
        }
    }
    
header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}