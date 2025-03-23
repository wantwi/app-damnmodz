<?php
//
///**
// * @ Server-side Config
// * App Name: DamnModz Portal
// * App Version: 0.0.01
// * Author: Clottware
// * Website: https://clottware.com
// * Support: clottware@gmail.com
// *
// */
//
//// Allowed origins for CORS
//// Allow from any origin
//$allowed_origins = ['https://portal.damnmodz.com', 'https://damnmodz.com', 'http://localhost:5173'];
////$allowed_domains = ['portal.damnmodz.com', 'damnmodz.com', 'localhost:5173'];
//
//// Check the Referer header
////if (isset($_SERVER['HTTP_REFERER'])) {
//    //$referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
//
//    // Debugging: Log the referer for testing
//   // error_log("Referer Host: " . $referer_host);
//
//   // if (!in_array($referer_host, $allowed_domains)) {
//       // http_response_code(403); // Forbidden
//       // echo json_encode([
//         //   "status" => false,
//        //]);
//        //exit;
//   // }
////} else {
//    // No Referer: Block the request
//    //http_response_code(403); // Forbidden
//    //echo json_encode([
//    //    "status" => false,
//    //]);
//   // exit;
////}
//
//
//
//
//
//if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
//    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
//    header('Access-Control-Allow-Credentials: true');
//    header("Access-Control-Allow-Headers: Content-Type, Authorization");
//    header('Access-Control-Max-Age: 86400');  // Cache for 1 day
//}


//
//if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");  // Add all necessary methods
//    }
//
//    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
//        header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With");
//    }
//
//    exit(0);
//}
//
////session_start();
//
// Database Defines
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_NAME', 'damnmodz');
define('DB_PASSWORD', '');
//
//// define('DB_HOST', 'localhost');
//// define('DB_USERNAME', 'ttgstaff_root');
//// define('DB_NAME', 'ttgstaff_database');
//// define('DB_PASSWORD', 'P*bGd4O9T481');
//
//// Database Connections
//require_once  __DIR__ . '/class/DatabaseConnect.php';
//require_once  __DIR__ . '/class/DatabaseHandler.php';
//require_once  __DIR__ . '/class/Request.php';
//$pdo = DatabaseConnection::connect();
//$dbHandler = new DatabaseHandler();
//$requestModel = new Request();
//
//
//// Emails
//// Emails
////define('HOST_SMTP', 'smtp.gmail.com');
////define('HOST_USERNAME', 'desmondapril13@gmail.com');
////define('HOST_PASSWORD', 'dozg ocyb ehlp aocm');
//
//
//$smtp = $dbHandler->selectData('app_system', 'system', 'smtp');
//$smtp_username = $dbHandler->selectData('app_system', 'system', 'smtp_username');
//$smtp_password = $dbHandler->selectData('app_system', 'system', 'smtp_password');
//
//define('HOST_SMTP', $smtp['value']);
//define('HOST_USERNAME', $smtp_username['value']);
//define('HOST_PASSWORD', $smtp_password['value']);
//
////woocommers
//$wc_store = $dbHandler->selectData('app_system', 'system', 'wc_store');
//$wc_ck = $dbHandler->selectData('app_system', 'system', 'wc_consumer_key');
//$wc_sk = $dbHandler->selectData('app_system', 'system', 'wc_secrete_key');
//
//define("WC_STORE", $wc_store['value']);
//define("WC_CONSUMER_KEY", $wc_ck['value']);
//define("WC_SECRETE_KEY", $wc_sk['value']);
//
//// Time Zone
//date_default_timezone_set('Africa/Accra');
//
////echo date("M d Y H:i:s");
//
////includes/require
//include __DIR__ . '/class/ApiHandler.php';
//require_once  __DIR__ . '/class/csrf_token.php';
//require_once  __DIR__ . '/class/Utils.php';
//require_once  __DIR__ . '/class/ResponseSigner.php';
//require_once  __DIR__ . '/functions/function.php';
//require_once  __DIR__ . '/functions/timeago.php';
//require_once  __DIR__ . '/functions/mailer.php';
//require_once  __DIR__ . '/functions/logs.php';
//require_once  __DIR__ . '/functions/notification.php';
//require_once  __DIR__ . '/functions/session.php';
//
//
////$apiHandler = new ApiHandler();
//$CSRFToken = new CSRFToken();
//$signature = new ResponseSigner();
//$utils = new Utils();
//$apiHandler = new ApiHandler();
//
//
//
//
// Defined keys
//define('JTW_KEY', 'JALi5WJBDTsLaEEE');