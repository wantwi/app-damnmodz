<?php
require_once '../config.php';

require '../vendor/autoload.php';
use \Firebase\JWT\JWT;

use PragmaRX\Google2FA\Google2FA;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

$currentDatetime = date("Y-m-d H:i:s");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
$key = "juhsuUSSASG698sdfsjk42DHADJASKDD5asdhjfdfjhDSNMDFSFSF"; // Use a strong secret key
$issuedAt = time();
    
    //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
        if($data) 
    {
        // Validate CSRF TOKEN
        if (isset($data['login_process']) && isset($data['token'])) {
            
            $rawToken = $data['token'] ?? '';
        
           if (CSRFToken::validateToken($rawToken)) {

                    $email = isset($data['email']) ? $data['email'] : '';
                    $password = isset($data['password']) ? $data['password'] : '';
                    
                    if(!empty($email) && !empty($password) && filter_var($email, FILTER_VALIDATE_EMAIL)){
                        //Check for existing user
                        $existingUser = $dbHandler->existingData('users', 'email', $email);
                        
                        
                        if($existingUser[0]=== 1){
                            $user = $dbHandler->selectData('users', 'email', $email);
                            
                         // Verify the password
                        if (password_verify($password, $user['password'])) {
                            $dbHandler-> updateData('users', 'last_seen', $currentDatetime, 'email', $email);
                            
                            if($user['account_status']==='pending'){
                                $dbHandler-> updateData('users', 'account_status', 'active', 'email', $email);
                            }
                            
                            if($user['account_status']==='suspended'){
                                $responseData = [
                                "status"=>false,
                                "message"=> "This account is suspended."
                                ];
                            }else{
                                
                                $dbHandler->onlineStatusUpdate('users', 'id', $user['id']);
                                
                                if(!empty($user['google_2fa'])){
                                    $hashId= $utils->getToken(32);
                                    
                                        $data = [
                                        "status"=>true,
                                        "timestamp" => time(),
                                        "data"=>[
                                            "id"=> $user['id'],
                                            "code"=> $hashId
                                            ],
                                        ];
                                        
                                        $responseData = ResponseSigner::signResponse($data);                                   
                                        
                                    
                                    newLogs('system', "$email logged in with two step authentication on.",'success');
                                }else{
                                    if($user['type'] == 'admin'){
                                        $expiration = $issuedAt + (30 * 24 * 60 * 60); // Token valid for 30 days (1 month)
                                        $url = '/admin/dashboard';
                                    }else if($user['type'] == 'user'){
                                        $expiration = $issuedAt + (30 * 24 * 60 * 60); // Token valid for 1 day
                                        $url = '/dashboard';
                                    }
                                    
                                    $payload = array(
                                        "iat" => $issuedAt,
                                        "exp" => $expiration,
                                        "user_id" => $user['id'],
                                        "user_email" => $user['email'],
                                        "type"=> $user['type'],
                                    );                    
                                    
                                    $jwt = JWT::encode($payload, $key, 'HS256');
                                    newLogs('system', "$email logged in successfully to portal.",'success');
                                    
                                        $data = [
                                        "status"=>true,
                                        "message"=> "User logged in successfully",
                                        "url"=>$url,
                                        "timestamp" => time(),
                                        "data"=>[
                                            "email"=> $user['email'],
                                            "token" => $jwt,
                                            "name"=> $user['name'],
                                            "type"=> $user['type'],
                                            "last_seen"=> $currentDatetime,
                                            ],
                                        ];
                                        
                                        $responseData = ResponseSigner::signResponse($data);
                                    
                                    
                                                                 
                                }
                               
                            }
                        }else{
                            newLogs('system', "Login attempt failed. Email: $email | Password: $password.",'error');
                            $responseData = [
                            "status"=>false,
                            "message"=> "Wrong email or password"
                            ];
                            
                        }
        
                        }else{
                            newLogs('system', "Login attempt failed. Email: $email | Password: $password.",'error');
                            $responseData = [
                            "status"=>false,
                            //"message"=> "Wrong email or password"
                            "message"=> "Portal under maintenance check back soon."
                            ];
                            
                        }
                    }else{
                         $responseData = [
                            "status"=>false,
                            "message"=> "Enter email and password"
                            ]; 
                    }
                    
            } else {
                 $responseData = [
                    "status"=>false,
                    "message"=> "Invalide token, Reload page."
                    ]; 
            }
        }
        
        if (isset($data['twoStep']) && isset($data['code'])) {
            if(!empty($data['id'])){
                $user = $dbHandler->selectData('users', 'id', $data['id']);
                // Initiate antonioribeiro/google2fa object
                $_g2fa = new Google2FA(); 
                
                $valid = $_g2fa->verifyKey($user['google_2fa'], $data['code']);
                
                if($valid){
                        
                            if($user['type'] == 'admin'){
                                $expiration = $issuedAt + (30 * 24 * 60 * 60); // Token valid for 30 days (1 month)
                                $url = '/admin/dashboard';
                            }else if($user['type'] == 'user'){
                                $expiration = $issuedAt + (30 * 24 * 60 * 60); // Token valid for 1 day
                                $url = '/dashboard';
                            }
                            
                            $payload = array(
                                "iat" => $issuedAt,
                                "exp" => $expiration,
                                "user_id" => $user['id'],
                                "user_email" => $user['email'],
                                "type"=> $user['type'],
                            );                    
                            
                            $jwt = JWT::encode($payload, $key, 'HS256');
                            newLogs('system', "$user[email] logged in successfully to portal.",'success');
                            $data = [
                            "status"=>true,
                            "message"=> "User logged in successfully",
                            "url"=>$url,
                            "timestamp" => time(),
                            "data"=>[
                                "email"=> $user['email'],
                                "token" => $jwt,
                                "name"=> $user['name'],
                                "type"=> $user['type'],
                                "last_seen"=> $currentDatetime,
                                ],
                            ];
                            
                        $responseData = ResponseSigner::signResponse($data);                                                 
                }else{
                    newLogs('system', "Login attempt failed. Wrong two step code",'error');
                    $responseData = [
                    "status"=>false,
                    "message"=> "Wrong code.",
                    ];                     
                }
                
            }else{
                $responseData = [
                "status"=>false,
                "message"=> "Time out",
                "url"=>'/auth/login',
                "data"=>[
                    "timeout"=> true,
                    ],
                ];                 
            }
        }
        
        if (isset($data['forgotPass']) && isset($data['token'])) {
            $email = isset($data['email']) ? $data['email'] : '';
            
            $user = $dbHandler->selectData('users', 'email', $email);
            
            if(!empty($user)){
                $password = $utils->getToken(16);
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $update = $dbHandler-> updateData('users', 'password', $hashedPassword, 'email', $data['email']);
                passwordReset($email, $user['name'], $password);
                newLogs('system', "$email requested new password.",'success');
            }
            
    
            $responseData = [
            "status"=>true,
            "message"=> "Check email for new password"
            ];
        }
    }
    
header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    // For 2 factor authentication
    
    if (isset($_GET['google_2fa'])) {
        $id = $_GET['google_2fa'];
        $user = $dbHandler->selectData('users', 'id', $id);
        
        if(!empty($user)){
                // Initiate antonioribeiro/google2fa object
                $_g2fa = new Google2FA();
                
                // Provide name of application (To display to user on app)
                $app_name = 'DamnModz Portal';
                $key = $_g2fa->generateSecretKey();
                
                $qrCodeUrl = $_g2fa->getQRCodeUrl(
                    $app_name,
                    $user['email'],
                    $key
                );        

                $writer = new PngWriter();
                
                $qrCode = QrCode::create($qrCodeUrl)
                    ->setEncoding(new Encoding('UTF-8'))
                    ->setSize(300)
                    ->setMargin(10)
                    ->setForegroundColor(new Color(0, 0, 0))
                    ->setBackgroundColor(new Color(255, 255, 255));
                
                $result = $writer->write($qrCode, null, null);
                
                $dataUri = $result->getDataUri();
                //$current_otp = $_g2fa->getCurrentOtp($key);
                newLogs('system', "QR code generated successfully for $user[email]",'success');
                if(!empty($dataUri)){
                    $responseData = [
                    "status" => true,
                    "code"=> $key,
                    "img" => $dataUri,
                    ];
                }
        }


    }
    

header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT'){
      //Check and process entered data
    $data = json_decode(file_get_contents('php://input'), true);
    $responseData= [];
        if($data)
        {
            if(isset($data['action']) && $data['action'] === 'new' && isset($data['code']) && isset($data['input'])){
                $user = $dbHandler->selectData('users', 'id', $data['id']);
                // Initiate antonioribeiro/google2fa object
                $_g2fa = new Google2FA();
                $window = 8;
                //$current_otp = $_g2fa->getCurrentOtp($data['code']);
                
                $valid = $_g2fa->verifyKey($data['code'], $data['input']);
                
                if($valid){
                    $dbHandler-> updateData('users', 'google_2fa', $data['code'], 'id', $data['id']);
                    if($user['type']=== 'admin'){
                        $url = '/admin/dashboard/account-settings';
                    }else{
                        $url = '/dashboard/account-settings';
                    }
                    newLogs('system', "2 factor authentication set successfully for $user[email]",'success');
                    $responseData = [
                        "status" => true,
                        "message"=> '2 factor authentication set successfully',
                        "url"=>$url
                    ];
                }else{
                    newLogs('system', "2 factor authentication set unsuccessfully for $user[email]. Wrong code",'error');
                    $responseData = [
                        "status" => false,
                        "message"=> 'Wrong code. Try again.',
                    ];                    
                }
            }
            
            if(isset($data['action']) && $data['action'] === 'del'){
                $user = $dbHandler->selectData('users', 'id', $data['id']);
                $update = $dbHandler-> updateData('users', 'google_2fa', null, 'id', $data['id']);
                if($user['type']=== 'admin'){
                    $url = '/admin/dashboard/account-settings';
                }else{
                    $url = '/dashboard/account-settings';
                }             
                 if($update){
                     newLogs('system', "2 factor authentication deleted successfully for $user[email]",'success');
                        $responseData = [
                        "status" => true,
                        "message"=> '2-Step verification deleted successfully',
                        "url" => $url,
                        ];
                 }else{
                     newLogs('system', "2 factor authentication delete was unsuccessfully for $user[email]",'error');
                        $responseData = [
                        "status" => false,
                        "message"=> 'Something went wrong. Contact admin@damnmodz.com for support',
                        "url" => $url,
                        ];                     
                 }
            }
        }

header('Content-Type: application/json');
echo json_encode($responseData);
exit();
}




