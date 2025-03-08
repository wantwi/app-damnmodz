<?php
// -------------------------------------------------------------------------------------------------------------------
// Project:     Google Authenticator (App) as 2-Factor Authorization using a timestamped one time password
// Technology:  PHP, JavaScript, HTML, CSS
// Author:      Clottware
// Website:     clottware.com
// -------------------------------------------------------------------------------------------------------------------

// Start Server Session
session_start();

// Display All Errors (For Easier Development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include composer packages
require './vendor/autoload.php';

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


// Initiate antonioribeiro/google2fa object
$_g2fa = new Google2FA();

// Generate a secret key and a test user
$user = new stdClass();
$user->google2fa_secret = $_g2fa->generateSecretKey();
$user->email = 'save@interactiveutopia.com';

// Store user data and key in the server session
$_SESSION['g2fa_user'] = $user;

// Provide name of application (To display to user on app)
$app_name = 'DamnModz Portal';

//$_g2fa->setAllowInsecureCallToGoogleApis(true);

// Generate a custom URL from user data to provide to qr code generator
$qrCodeUrl = $_g2fa->getQRCodeUrl(
    $app_name,
    $user->email,
    $user->google2fa_secret
);

$writer = new PngWriter();

$qrCode = QrCode::create($qrCodeUrl)
    ->setEncoding(new Encoding('UTF-8'))
    ->setSize(300)
    ->setMargin(10)
    ->setForegroundColor(new Color(0, 0, 0))
    ->setBackgroundColor(new Color(255, 255, 255));

$result = $writer->write($qrCode, null, null);

$current_otp = $_g2fa->getCurrentOtp($user->google2fa_secret);

$dataUri = $result->getDataUri();

//echo $dataUri;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA's</title>
</head>

<body>
    <p><?=$current_otp?></p></p>
    <img src="<?= $dataUri ?>" alt="">
    <br>
    <br>
    <?= print_r($_SESSION['g2fa_user'])?>
    <p>Or enter code <?=$user->google2fa_secret?></p>
</body>
