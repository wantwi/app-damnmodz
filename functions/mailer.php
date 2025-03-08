<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require __DIR__ . '/../vendor/autoload.php';

function sendWelcomeEmail($email, $name, $password)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = 'Welcome to DamnModz Portal';
    $mail->Body = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
<head>
<!--[if gte mso 9]>
<xml>
  <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml>
<![endif]-->
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <meta name='x-apple-disable-message-reformatting'>
  <!--[if !mso]><!--><meta http-equiv='X-UA-Compatible' content='IE=edge'><!--<![endif]-->
  <title></title>
  
    <style type='text/css'>
      
      @media only screen and (min-width: 520px) {
        .u-row {
          width: 500px !important;
        }

        .u-row .u-col {
          vertical-align: top;
        }

        
            .u-row .u-col-100 {
              width: 500px !important;
            }
          
      }

      @media only screen and (max-width: 520px) {
        .u-row-container {
          max-width: 100% !important;
          padding-left: 0px !important;
          padding-right: 0px !important;
        }

        .u-row {
          width: 100% !important;
        }

        .u-row .u-col {
          display: block !important;
          width: 100% !important;
          min-width: 320px !important;
          max-width: 100% !important;
        }

        .u-row .u-col > div {
          margin: 0 auto;
        }


        .u-row .u-col img {
          max-width: 100% !important;
        }

}
    
body{margin:0;padding:0}table,td,tr{border-collapse:collapse;vertical-align:top}p{margin:0}.ie-container table,.mso-container table{table-layout:fixed}*{line-height:inherit}a[x-apple-data-detectors=true]{color:inherit!important;text-decoration:none!important}


table, td { color: #000000; } #u_body a { color: #0000ee; text-decoration: underline; } @media (max-width: 480px) { #u_content_image_1 .v-src-width { width: auto !important; } #u_content_image_1 .v-src-max-width { max-width: 63% !important; } }
    </style>
  
  

</head>

<body class='clean-body u_body' style='margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #ffffff;color: #000000'>
  <!--[if IE]><div class='ie-container'><![endif]-->
  <!--[if mso]><div class='mso-container'><![endif]-->
  <table id='u_body' style='border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #ffffff;width:100%' cellpadding='0' cellspacing='0'>
  <tbody>
  <tr style='vertical-align: top'>
    <td style='word-break: break-word;border-collapse: collapse !important;vertical-align: top'>
    <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td align='center' style='background-color: #ffffff;'><![endif]-->
    
  
  
<div class='u-row-container' style='padding: 0px;background-color: #001a38'>
  <div class='u-row' style='margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #001a38;'>
    <div style='border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;'>
      <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: #001a38;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: #001a38;'><![endif]-->
      
<!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #001a38;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
<div class='u-col u-col-100' style='max-width: 320px;min-width: 500px;display: table-cell;vertical-align: top;'>
  <div style='background-color: #001a38;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;'>
  <!--[if (!mso)&(!IE)]><!--><div style='box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;'><!--<![endif]-->
  
<table id='u_content_image_1' style='font-family:arial,helvetica,sans-serif;' role='presentation' cellpadding='0' cellspacing='0' width='100%' border='0'>
  <tbody>
    <tr>
      <td style='overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:arial,helvetica,sans-serif;' align='left'>
        
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td style='padding-right: 0px;padding-left: 0px;' align='center'>
      
      <img align='center' border='0' src='https://app.damnmodz.com/images/system/damnmodz-logo.png' alt='' title='' style='outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 100%;max-width: 236px;' width='236' class='v-src-width v-src-max-width'/>
      
    </td>
  </tr>
</table>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
  </div>
  


  
  
<div class='u-row-container' style='padding: 0px;background-color: transparent'>
  <div class='u-row' style='margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;'>
    <div style='border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;'>
      <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->
      
<!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
<div class='u-col u-col-100' style='max-width: 320px;min-width: 500px;display: table-cell;vertical-align: top;'>
  <div style='background-color: #ffffff;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;'>
  <!--[if (!mso)&(!IE)]><!--><div style='box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;'><!--<![endif]-->
  
<table style='font-family:arial,helvetica,sans-serif;' role='presentation' cellpadding='0' cellspacing='0' width='100%' border='0'>
  <tbody>
    <tr>
      <td style='overflow-wrap:break-word;word-break:break-word;padding:38px 10px 10px;font-family:arial,helvetica,sans-serif;' align='left'>
        
  <div style='font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;'>
    <p style='line-height: 140%;'>Hello $name</p>
  </div>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
  </div>
  


  
  
<div class='u-row-container' style='padding: 0px;background-color: transparent'>
  <div class='u-row' style='margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;'>
    <div style='border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;'>
      <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->
      
<!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
<div class='u-col u-col-100' style='max-width: 320px;min-width: 500px;display: table-cell;vertical-align: top;'>
  <div style='background-color: #ffffff;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;'>
  <!--[if (!mso)&(!IE)]><!--><div style='box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;'><!--<![endif]-->
  
<table style='font-family:arial,helvetica,sans-serif;' role='presentation' cellpadding='0' cellspacing='0' width='100%' border='0'>
  <tbody>
    <tr>
      <td style='overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:arial,helvetica,sans-serif;' align='left'>
        
  <div style='font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;'>
    <p style='line-height: 140%;'>We are excited to have you on our platform. Below are your login details:</p>
  </div>

      </td>
    </tr>
  </tbody>
</table>

<table style='font-family:arial,helvetica,sans-serif;' role='presentation' cellpadding='0' cellspacing='0' width='100%' border='0'>
  <tbody>
    <tr>
      <td style='overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:arial,helvetica,sans-serif;' align='left'>
        
  <div style='font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;'>
    <p style='line-height: 140%;'>Email: $email</p>
<p style='line-height: 140%;'>Password: $password</p>
  </div>

      </td>
    </tr>
  </tbody>
</table>

<table style='font-family:arial,helvetica,sans-serif;' role='presentation' cellpadding='0' cellspacing='0' width='100%' border='0'>
  <tbody>
    <tr>
      <td style='overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:arial,helvetica,sans-serif;' align='left'>
        
  <div style='font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;'>
    <p style='line-height: 140%;'>You can change your password anytime. Click on the button below to login</p>
  </div>

      </td>
    </tr>
  </tbody>
</table>

<table style='font-family:arial,helvetica,sans-serif;' role='presentation' cellpadding='0' cellspacing='0' width='100%' border='0'>
  <tbody>
    <tr>
      <td style='overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:arial,helvetica,sans-serif;' align='left'>
        
  <div style='font-size: 14px; line-height: 140%; text-align: center; word-wrap: break-word;'>
    <p style='line-height: 140%; text-align: center;'><span style='font-size: 10px; line-height: 14px;'>Experiencing issues with an order? </span></p>
<p style='line-height: 140%; text-align: center;'><span style='font-size: 10px; line-height: 14px;'>Send us  a mail <a rel='noopener' href='mailto:admin@damnmodz.com?subject=Support' target='_blank'>admin@damnmodz.com</a></span></p>
  </div>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
  </div>
  


    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
    </td>
  </tr>
  </tbody>
  </table>
  <!--[if mso]></div><![endif]-->
  <!--[if IE]></div><![endif]-->
</body>

</html>
";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function newOrder($email, $name)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = 'New Order';
    $mail->Body = "<!-- @format -->

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html
  xmlns='http://www.w3.org/1999/xhtml'
  xmlns:v='urn:schemas-microsoft-com:vml'
  xmlns:o='urn:schemas-microsoft-com:office:office'
>
  <head>
    <!--[if gte mso 9]>
      <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG />
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
      </xml>
    <![endif]-->
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta name='x-apple-disable-message-reformatting' />
    <!--[if !mso]><!-->
    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
    <!--<![endif]-->
    <title></title>

    <style type='text/css'>
      @media only screen and (min-width: 520px) {
        .u-row {
          width: 500px !important;
        }

        .u-row .u-col {
          vertical-align: top;
        }

        .u-row .u-col-100 {
          width: 500px !important;
        }
      }

      @media only screen and (max-width: 520px) {
        .u-row-container {
          max-width: 100% !important;
          padding-left: 0px !important;
          padding-right: 0px !important;
        }

        .u-row {
          width: 100% !important;
        }

        .u-row .u-col {
          display: block !important;
          width: 100% !important;
          min-width: 320px !important;
          max-width: 100% !important;
        }

        .u-row .u-col > div {
          margin: 0 auto;
        }

        .u-row .u-col img {
          max-width: 100% !important;
        }
      }

      body {
        margin: 0;
        padding: 0;
      }
      table,
      td,
      tr {
        border-collapse: collapse;
        vertical-align: top;
      }
      p {
        margin: 0;
      }
      .ie-container table,
      .mso-container table {
        table-layout: fixed;
      }
      * {
        line-height: inherit;
      }
      a[x-apple-data-detectors='true'] {
        color: inherit !important;
        text-decoration: none !important;
      }

      table,
      td {
        color: #000000;
      }
      #u_body a {
        color: #0000ee;
        text-decoration: underline;
      }
      @media (max-width: 480px) {
        #u_content_image_1 .v-src-width {
          width: auto !important;
        }
        #u_content_image_1 .v-src-max-width {
          max-width: 63% !important;
        }
      }
    </style>
  </head>

  <body
    class='clean-body u_body'
    style='
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      background-color: #ffffff;
      color: #000000;
    '
  >
    <!--[if IE]><div class='ie-container'><![endif]-->
    <!--[if mso]><div class='mso-container'><![endif]-->
    <table
      id='u_body'
      style='
        border-collapse: collapse;
        table-layout: fixed;
        border-spacing: 0;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        vertical-align: top;
        min-width: 320px;
        margin: 0 auto;
        background-color: #ffffff;
        width: 100%;
      '
      cellpadding='0'
      cellspacing='0'
    >
      <tbody>
        <tr style='vertical-align: top'>
          <td
            style='
              word-break: break-word;
              border-collapse: collapse !important;
              vertical-align: top;
            '
          >
            <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td align='center' style='background-color: #ffffff;'><![endif]-->

            <div
              class='u-row-container'
              style='padding: 0px; background-color: #001a38'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: #001a38;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: #001a38;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: #001a38;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #001a38;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #001a38;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          id='u_content_image_1'
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <table
                                  width='100%'
                                  cellpadding='0'
                                  cellspacing='0'
                                  border='0'
                                >
                                  <tr>
                                    <td
                                      style='
                                        padding-right: 0px;
                                        padding-left: 0px;
                                      '
                                      align='center'
                                    >
                                      <img
                                        align='center'
                                        border='0'
                                        src='https://app.damnmodz.com/images/system/damnmodz-logo.png'
                                        alt=''
                                        title=''
                                        style='
                                          outline: none;
                                          text-decoration: none;
                                          -ms-interpolation-mode: bicubic;
                                          clear: both;
                                          display: inline-block !important;
                                          border: none;
                                          height: auto;
                                          float: none;
                                          width: 100%;
                                          max-width: 236px;
                                        '
                                        width='236'
                                        class='v-src-width v-src-max-width'
                                      />
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 38px 10px 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>Hello $name</p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>
                                    A new order just arrived. Click on the
                                    button below to login to your portal
                                    dashboard to see more.
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <!--[if mso
                                  ]><style>
                                    .v-button {
                                      background: transparent !important;
                                    }
                                  </style><!
                                [endif]-->
                                <div align='center'>
                                  <!--[if mso]><v:roundrect xmlns:v='urn:schemas-microsoft-com:vml' xmlns:w='urn:schemas-microsoft-com:office:word' href='' style='height:37px; v-text-anchor:middle; width:80px;' arcsize='11%'  stroke='f' fillcolor='#001a38'><w:anchorlock/><center style='color:#ffffff;'><![endif]-->
                                  <a
                                    href='https://portal.damnmodz.com/auth/login'
                                    target='_blank'
                                    class='v-button'
                                    style='
                                      box-sizing: border-box;
                                      display: inline-block;
                                      text-decoration: none;
                                      -webkit-text-size-adjust: none;
                                      text-align: center;
                                      color: #ffffff;
                                      background-color: #001a38;
                                      border-radius: 4px;
                                      -webkit-border-radius: 4px;
                                      -moz-border-radius: 4px;
                                      width: auto;
                                      max-width: 100%;
                                      overflow-wrap: break-word;
                                      word-break: break-word;
                                      word-wrap: break-word;
                                      mso-border-alt: none;
                                      font-size: 14px;
                                    '
                                  >
                                    <span
                                      style='
                                        display: block;
                                        padding: 10px 20px;
                                        line-height: 120%;
                                      '
                                      ><span
                                        style='
                                          font-size: 14px;
                                          line-height: 16.8px;
                                        '
                                        >Login</span
                                      ></span
                                    >
                                  </a>
                                  <!--[if mso]></center></v:roundrect><![endif]-->
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: center;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Experiencing issues with an order?
                                    </span>
                                  </p>
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Send us  a mail
                                      <a
                                        rel='noopener'
                                        href='mailto:admin@damnmodz.com?subject=Support'
                                        target='_blank'
                                        >admin@damnmodz.com</a
                                      ></span
                                    >
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
    <!--[if mso]></div><![endif]-->
    <!--[if IE]></div><![endif]-->
  </body>
</html>

 ";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function adminPasswordReset($email, $name, $password)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = 'Password Reset';
    $mail->Body = "<html>
    <head>
    <title>Welcome to Our Platform</title>
    </head>
    <body>
    <p>Hello, $name!</p>
    <p>Your password has been updated successfully. Below are your login details:</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Password:</strong> $password</p>
    <p>You can log in and change your password anytime.</p>
    <p>Best regards,<br>DamnModz</p>
    </body>
    </html> ";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function orderAcceptance($email, $supplier, $product, $order_number, $price)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = "New Order Accepted by $supplier -  Order #$order_number";
    $mail->Body = "<html>
    <head>
    <title>New Order Accepted by $supplier -  Order #$order_number</title>
    </head>
    <body>
    <p>A new order has been accepted by $supplier:</p>
    <p><strong>Order ID:</strong> $order_number</p>
    <p><strong>Product:</strong> $product</p>
    <p><strong>Price:</strong> $price</p>
    </body>
    </html> ";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function orderCompleted($email, $supplier, $product, $order_number, $price, $funds, $date)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = "Order #$order_number completed";
    $mail->Body = "<html>
    <head>
    <title>Order Completed: Order #$order_number</title>
    </head>
    <body>
    <p><strong>Supplier:</strong> $supplier</p>
    <p><strong>Order ID:</strong> $order_number</p>
    <p><strong>Product:</strong> $product</p>
    <p><strong>Price:</strong> $price</p>
    <p><strong>Completion Date:</strong> $date</p>
    <p><strong>Pending Funds:</strong> $funds</p>
    <p><strong>Login to the dashbaord to accept/decline pending funds</p>
    </body>
    </html> ";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function payoutRequest($email, $supplier, $amount, $date)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = "Payout Request Received from $supplier";
    $mail->Body = "<html>
    <head>
    <title>Payout Request Received from $supplier</title>
    </head>
    <body>
    <p>A new payout request from $supplier. Please review the details below and take the necessary actions.</p>
    <p><strong>Supplier Name:</strong> $supplier</p>
    <p><strong>Payout Amount:</strong> $amount</p>
    <p><strong>Request Date:</strong> $date</p>
    <p><strong>Please log in to your admin dashboard to process this payout request or to get more information.</p>
    </body>
    </html> ";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function newChat($email, $name, $order_number, $order_key)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = "New chat for -  Order #$order_number";
    $mail->Body = "<!-- @format -->

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html
  xmlns='http://www.w3.org/1999/xhtml'
  xmlns:v='urn:schemas-microsoft-com:vml'
  xmlns:o='urn:schemas-microsoft-com:office:office'
>
  <head>
    <!--[if gte mso 9]>
      <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG />
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
      </xml>
    <![endif]-->
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta name='x-apple-disable-message-reformatting' />
    <!--[if !mso]><!-->
    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
    <!--<![endif]-->
    <title></title>

    <style type='text/css'>
      @media only screen and (min-width: 520px) {
        .u-row {
          width: 500px !important;
        }

        .u-row .u-col {
          vertical-align: top;
        }

        .u-row .u-col-100 {
          width: 500px !important;
        }
      }

      @media only screen and (max-width: 520px) {
        .u-row-container {
          max-width: 100% !important;
          padding-left: 0px !important;
          padding-right: 0px !important;
        }

        .u-row {
          width: 100% !important;
        }

        .u-row .u-col {
          display: block !important;
          width: 100% !important;
          min-width: 320px !important;
          max-width: 100% !important;
        }

        .u-row .u-col > div {
          margin: 0 auto;
        }

        .u-row .u-col img {
          max-width: 100% !important;
        }
      }

      body {
        margin: 0;
        padding: 0;
      }
      table,
      td,
      tr {
        border-collapse: collapse;
        vertical-align: top;
      }
      p {
        margin: 0;
      }
      .ie-container table,
      .mso-container table {
        table-layout: fixed;
      }
      * {
        line-height: inherit;
      }
      a[x-apple-data-detectors='true'] {
        color: inherit !important;
        text-decoration: none !important;
      }

      table,
      td {
        color: #000000;
      }
      #u_body a {
        color: #0000ee;
        text-decoration: underline;
      }
      @media (max-width: 480px) {
        #u_content_image_1 .v-src-width {
          width: auto !important;
        }
        #u_content_image_1 .v-src-max-width {
          max-width: 63% !important;
        }
      }
    </style>
  </head>

  <body
    class='clean-body u_body'
    style='
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      background-color: #ffffff;
      color: #000000;
    '
  >
    <!--[if IE]><div class='ie-container'><![endif]-->
    <!--[if mso]><div class='mso-container'><![endif]-->
    <table
      id='u_body'
      style='
        border-collapse: collapse;
        table-layout: fixed;
        border-spacing: 0;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        vertical-align: top;
        min-width: 320px;
        margin: 0 auto;
        background-color: #ffffff;
        width: 100%;
      '
      cellpadding='0'
      cellspacing='0'
    >
      <tbody>
        <tr style='vertical-align: top'>
          <td
            style='
              word-break: break-word;
              border-collapse: collapse !important;
              vertical-align: top;
            '
          >
            <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td align='center' style='background-color: #ffffff;'><![endif]-->

            <div
              class='u-row-container'
              style='padding: 0px; background-color: #001a38'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: #001a38;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: #001a38;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: #001a38;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #001a38;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #001a38;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          id='u_content_image_1'
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <table
                                  width='100%'
                                  cellpadding='0'
                                  cellspacing='0'
                                  border='0'
                                >
                                  <tr>
                                    <td
                                      style='
                                        padding-right: 0px;
                                        padding-left: 0px;
                                      '
                                      align='center'
                                    >
                                      <img
                                        align='center'
                                        border='0'
                                        src='https://app.damnmodz.com/images/system/damnmodz-logo.png'
                                        alt=''
                                        title=''
                                        style='
                                          outline: none;
                                          text-decoration: none;
                                          -ms-interpolation-mode: bicubic;
                                          clear: both;
                                          display: inline-block !important;
                                          border: none;
                                          height: auto;
                                          float: none;
                                          width: 100%;
                                          max-width: 236px;
                                        '
                                        width='236'
                                        class='v-src-width v-src-max-width'
                                      />
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 38px 10px 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>Hello $name</p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>
                                    You have a new message. Click on the button
                                    below to view the message.
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <!--[if mso
                                  ]><style>
                                    .v-button {
                                      background: transparent !important;
                                    }
                                  </style><!
                                [endif]-->
                                <div align='center'>
                                  <!--[if mso]><v:roundrect xmlns:v='urn:schemas-microsoft-com:vml' xmlns:w='urn:schemas-microsoft-com:office:word' href='' style='height:37px; v-text-anchor:middle; width:80px;' arcsize='11%'  stroke='f' fillcolor='#001a38'><w:anchorlock/><center style='color:#ffffff;'><![endif]-->
                                  <a
                                    href='https://portal.damnmodz.com/order/$order_key/$order_number'
                                    target='_blank'
                                    class='v-button'
                                    style='
                                      box-sizing: border-box;
                                      display: inline-block;
                                      text-decoration: none;
                                      -webkit-text-size-adjust: none;
                                      text-align: center;
                                      color: #ffffff;
                                      background-color: #001a38;
                                      border-radius: 4px;
                                      -webkit-border-radius: 4px;
                                      -moz-border-radius: 4px;
                                      width: auto;
                                      max-width: 100%;
                                      overflow-wrap: break-word;
                                      word-break: break-word;
                                      word-wrap: break-word;
                                      mso-border-alt: none;
                                      font-size: 14px;
                                    '
                                  >
                                    <span
                                      style='
                                        display: block;
                                        padding: 10px 20px;
                                        line-height: 120%;
                                      '
                                      ><span
                                        style='
                                          font-size: 14px;
                                          line-height: 16.8px;
                                        '
                                        >Reply message</span
                                      ></span
                                    >
                                  </a>
                                  <!--[if mso]></center></v:roundrect><![endif]-->
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: center;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Experiencing issues with an order?
                                    </span>
                                  </p>
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Send us a mail
                                      <a
                                        rel='noopener'
                                        href='mailto:admin@damnmodz.com?subject=Support'
                                        target='_blank'
                                        >admin@damnmodz.com</a
                                      ></span
                                    >
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
    <!--[if mso]></div><![endif]-->
    <!--[if IE]></div><![endif]-->
  </body>
</html>

";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function newChats($email, $order_number, $url)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = "New chat for -  Order #$order_number";
    $mail->Body = "<!-- @format -->

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html
  xmlns='http://www.w3.org/1999/xhtml'
  xmlns:v='urn:schemas-microsoft-com:vml'
  xmlns:o='urn:schemas-microsoft-com:office:office'
>
  <head>
    <!--[if gte mso 9]>
      <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG />
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
      </xml>
    <![endif]-->
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta name='x-apple-disable-message-reformatting' />
    <!--[if !mso]><!-->
    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
    <!--<![endif]-->
    <title></title>

    <style type='text/css'>
      @media only screen and (min-width: 520px) {
        .u-row {
          width: 500px !important;
        }

        .u-row .u-col {
          vertical-align: top;
        }

        .u-row .u-col-100 {
          width: 500px !important;
        }
      }

      @media only screen and (max-width: 520px) {
        .u-row-container {
          max-width: 100% !important;
          padding-left: 0px !important;
          padding-right: 0px !important;
        }

        .u-row {
          width: 100% !important;
        }

        .u-row .u-col {
          display: block !important;
          width: 100% !important;
          min-width: 320px !important;
          max-width: 100% !important;
        }

        .u-row .u-col > div {
          margin: 0 auto;
        }

        .u-row .u-col img {
          max-width: 100% !important;
        }
      }

      body {
        margin: 0;
        padding: 0;
      }
      table,
      td,
      tr {
        border-collapse: collapse;
        vertical-align: top;
      }
      p {
        margin: 0;
      }
      .ie-container table,
      .mso-container table {
        table-layout: fixed;
      }
      * {
        line-height: inherit;
      }
      a[x-apple-data-detectors='true'] {
        color: inherit !important;
        text-decoration: none !important;
      }

      table,
      td {
        color: #000000;
      }
      #u_body a {
        color: #0000ee;
        text-decoration: underline;
      }
      @media (max-width: 480px) {
        #u_content_image_1 .v-src-width {
          width: auto !important;
        }
        #u_content_image_1 .v-src-max-width {
          max-width: 63% !important;
        }
      }
    </style>
  </head>

  <body
    class='clean-body u_body'
    style='
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      background-color: #ffffff;
      color: #000000;
    '
  >
    <!--[if IE]><div class='ie-container'><![endif]-->
    <!--[if mso]><div class='mso-container'><![endif]-->
    <table
      id='u_body'
      style='
        border-collapse: collapse;
        table-layout: fixed;
        border-spacing: 0;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        vertical-align: top;
        min-width: 320px;
        margin: 0 auto;
        background-color: #ffffff;
        width: 100%;
      '
      cellpadding='0'
      cellspacing='0'
    >
      <tbody>
        <tr style='vertical-align: top'>
          <td
            style='
              word-break: break-word;
              border-collapse: collapse !important;
              vertical-align: top;
            '
          >
            <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td align='center' style='background-color: #ffffff;'><![endif]-->

            <div
              class='u-row-container'
              style='padding: 0px; background-color: #001a38'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: #001a38;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: #001a38;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: #001a38;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #001a38;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #001a38;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          id='u_content_image_1'
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <table
                                  width='100%'
                                  cellpadding='0'
                                  cellspacing='0'
                                  border='0'
                                >
                                  <tr>
                                    <td
                                      style='
                                        padding-right: 0px;
                                        padding-left: 0px;
                                      '
                                      align='center'
                                    >
                                      <img
                                        align='center'
                                        border='0'
                                        src='https://app.damnmodz.com/images/system/damnmodz-logo.png'
                                        alt=''
                                        title=''
                                        style='
                                          outline: none;
                                          text-decoration: none;
                                          -ms-interpolation-mode: bicubic;
                                          clear: both;
                                          display: inline-block !important;
                                          border: none;
                                          height: auto;
                                          float: none;
                                          width: 100%;
                                          max-width: 236px;
                                        '
                                        width='236'
                                        class='v-src-width v-src-max-width'
                                      />
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 38px 10px 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>Hello $name</p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>
                                    You have a new message. Click on the button
                                    below to view the message.
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <!--[if mso
                                  ]><style>
                                    .v-button {
                                      background: transparent !important;
                                    }
                                  </style><!
                                [endif]-->
                                <div align='center'>
                                  <!--[if mso]><v:roundrect xmlns:v='urn:schemas-microsoft-com:vml' xmlns:w='urn:schemas-microsoft-com:office:word' href='' style='height:37px; v-text-anchor:middle; width:80px;' arcsize='11%'  stroke='f' fillcolor='#001a38'><w:anchorlock/><center style='color:#ffffff;'><![endif]-->
                                  <a
                                    href='https://portal.damnmodz.com/$url'
                                    target='_blank'
                                    class='v-button'
                                    style='
                                      box-sizing: border-box;
                                      display: inline-block;
                                      text-decoration: none;
                                      -webkit-text-size-adjust: none;
                                      text-align: center;
                                      color: #ffffff;
                                      background-color: #001a38;
                                      border-radius: 4px;
                                      -webkit-border-radius: 4px;
                                      -moz-border-radius: 4px;
                                      width: auto;
                                      max-width: 100%;
                                      overflow-wrap: break-word;
                                      word-break: break-word;
                                      word-wrap: break-word;
                                      mso-border-alt: none;
                                      font-size: 14px;
                                    '
                                  >
                                    <span
                                      style='
                                        display: block;
                                        padding: 10px 20px;
                                        line-height: 120%;
                                      '
                                      ><span
                                        style='
                                          font-size: 14px;
                                          line-height: 16.8px;
                                        '
                                        >Reply message</span
                                      ></span
                                    >
                                  </a>
                                  <!--[if mso]></center></v:roundrect><![endif]-->
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: center;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Experiencing issues with an order?
                                    </span>
                                  </p>
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Send us  a mail
                                      <a
                                        rel='noopener'
                                        href='mailto:admin@damnmodz.com?subject=Support'
                                        target='_blank'
                                        >admin@damnmodz.com</a
                                      ></span
                                    >
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
    <!--[if mso]></div><![endif]-->
    <!--[if IE]></div><![endif]-->
  </body>
</html>

";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function chatNotification($email, $name, $unseenCount)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = HOST_SMTP;
        $mail->SMTPAuth   = true;
        $mail->Username   = HOST_USERNAME;
        $mail->Password   = HOST_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

        // Recipients
        $mail->setFrom(HOST_USERNAME, 'DamnModz');
        $mail->addAddress($email);     // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = "You have $unseenCount unseen messages - DamnModz";
        $mail->Body    = "
            <html>
            <head>
                <title>You have $unseenCount unseen messages</title>
            </head>
            <body>
                <p>Hello $name,</p>
                <p>You have $unseenCount unseen messages in your chat inbox. Please check your messages to stay updated.</p>
                <p>To log in to DamnModz Portal, <a href='https://portal.damnmodz.com'>Click here</a></p>
                <p>Best regards,<br>DamnModz</p>
            </body>
            </html>";

        return $mail->send();
    } catch (Exception $e) {
        // Log the error if needed and return false
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

function orderNotification($email, $name, $unseenCount, $url)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = HOST_SMTP;
        $mail->SMTPAuth   = true;
        $mail->Username   = HOST_USERNAME;
        $mail->Password   = HOST_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

        // Recipients
        $mail->setFrom(HOST_USERNAME, 'DamnModz');
        $mail->addAddress($email);     // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = "You have $unseenCount unseen new orders - DamnModz";
        $mail->Body    = "
            <html>
            <head>
                <title>You have $unseenCount unseen new orders</title>
            </head>
            <body>
                <p>Hello $name,</p>
                <p>You have $unseenCount unseen new orders on the portal. Please check the portal to accept an order.</p>
                <p>To log in to DamnModz Portal, <a href='https://portal.damnmodz.com/$url'>Click here</a></p>
                <p>Best regards,<br>DamnModz</p>
            </body>
            </html>";

        return $mail->send();
    } catch (Exception $e) {
        // Log the error if needed and return false
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

function falgged($email,$message, $order_number, $order_key)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = "Order #$order_number Flagged";
    $mail->Body = "<html>
    <head>
    <title>Order #$order_number was flagged</title>
    </head>
    <body>
     <p>Order #$order_number was flagged. Reason:</p>
     <p>$message</p>
     
    <p><a href='https://portal.damnmodz.com/admin/dashboard/order/$order_key'>Click here</a> to check order.</p>
    <p>Best regards,<br>DamnModz</p>
    </body>
    </html> ";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function passwordReset($email, $name, $password)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = 'Password Reset';
    $mail->Body = "<html>
    <head>
    <title>Welcome to Our Platform</title>
    </head>
    <body>
    <h1>Hello $name,</h1>
    <p>Your password has been updated successfully. Below are your login details:</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Password:</strong> $password</p>
    <p>You can log in and change your password anytime.</p>
    <p>Best regards,<br>DamnModz</p>
    </body>
    </html> ";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function partialDelivery($email, $name, $product, $order_number)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = "DamnModz / Order#$order_number Partially Completed";
    $mail->Body = "<!-- @format -->

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html
  xmlns='http://www.w3.org/1999/xhtml'
  xmlns:v='urn:schemas-microsoft-com:vml'
  xmlns:o='urn:schemas-microsoft-com:office:office'
>
  <head>
    <!--[if gte mso 9]>
      <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG />
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
      </xml>
    <![endif]-->
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta name='x-apple-disable-message-reformatting' />
    <!--[if !mso]><!-->
    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
    <!--<![endif]-->
    <title></title>

    <style type='text/css'>
      @media only screen and (min-width: 520px) {
        .u-row {
          width: 500px !important;
        }

        .u-row .u-col {
          vertical-align: top;
        }

        .u-row .u-col-100 {
          width: 500px !important;
        }
      }

      @media only screen and (max-width: 520px) {
        .u-row-container {
          max-width: 100% !important;
          padding-left: 0px !important;
          padding-right: 0px !important;
        }

        .u-row {
          width: 100% !important;
        }

        .u-row .u-col {
          display: block !important;
          width: 100% !important;
          min-width: 320px !important;
          max-width: 100% !important;
        }

        .u-row .u-col > div {
          margin: 0 auto;
        }

        .u-row .u-col img {
          max-width: 100% !important;
        }
      }

      body {
        margin: 0;
        padding: 0;
      }
      table,
      td,
      tr {
        border-collapse: collapse;
        vertical-align: top;
      }
      p {
        margin: 0;
      }
      .ie-container table,
      .mso-container table {
        table-layout: fixed;
      }
      * {
        line-height: inherit;
      }
      a[x-apple-data-detectors='true'] {
        color: inherit !important;
        text-decoration: none !important;
      }

      table,
      td {
        color: #000000;
      }
      #u_body a {
        color: #0000ee;
        text-decoration: underline;
      }
      @media (max-width: 480px) {
        #u_content_image_1 .v-src-width {
          width: auto !important;
        }
        #u_content_image_1 .v-src-max-width {
          max-width: 63% !important;
        }
      }
    </style>
  </head>

  <body
    class='clean-body u_body'
    style='
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      background-color: #ffffff;
      color: #000000;
    '
  >
    <!--[if IE]><div class='ie-container'><![endif]-->
    <!--[if mso]><div class='mso-container'><![endif]-->
    <table
      id='u_body'
      style='
        border-collapse: collapse;
        table-layout: fixed;
        border-spacing: 0;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        vertical-align: top;
        min-width: 320px;
        margin: 0 auto;
        background-color: #ffffff;
        width: 100%;
      '
      cellpadding='0'
      cellspacing='0'
    >
      <tbody>
        <tr style='vertical-align: top'>
          <td
            style='
              word-break: break-word;
              border-collapse: collapse !important;
              vertical-align: top;
            '
          >
            <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td align='center' style='background-color: #ffffff;'><![endif]-->

            <div
              class='u-row-container'
              style='padding: 0px; background-color: #001a38'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: #001a38;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: #001a38;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: #001a38;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #001a38;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #001a38;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          id='u_content_image_1'
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <table
                                  width='100%'
                                  cellpadding='0'
                                  cellspacing='0'
                                  border='0'
                                >
                                  <tr>
                                    <td
                                      style='
                                        padding-right: 0px;
                                        padding-left: 0px;
                                      '
                                      align='center'
                                    >
                                      <img
                                        align='center'
                                        border='0'
                                        src='https://app.damnmodz.com/images/system/damnmodz-logo.png'
                                        alt=''
                                        title=''
                                        style='
                                          outline: none;
                                          text-decoration: none;
                                          -ms-interpolation-mode: bicubic;
                                          clear: both;
                                          display: inline-block !important;
                                          border: none;
                                          height: auto;
                                          float: none;
                                          width: 100%;
                                          max-width: 236px;
                                        '
                                        width='236'
                                        class='v-src-width v-src-max-width'
                                      />
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 38px 10px 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>Hello $name</p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>
                                    We are pleased to inform you that part of
                                    your recent order, $product, has been
                                    successfully delivered. However, we're still
                                    working on the half. Please do not login
                                    till you receive another update from us.
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: center;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Experiencing issues with an order?
                                    </span>
                                  </p>
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Send us a mail
                                      <a
                                        rel='noopener'
                                        href='mailto:admin@damnmodz.com?subject=Support'
                                        target='_blank'
                                        >admin@damnmodz.com</a
                                      ></span
                                    >
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
    <!--[if mso]></div><![endif]-->
    <!--[if IE]></div><![endif]-->
  </body>
</html>
";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function reviewOrder($email, $name, $product, $order_number)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress($email);     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = "DamnModz - Order Update #$order_number";
    $mail->Body = "<!-- @format -->

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html
  xmlns='http://www.w3.org/1999/xhtml'
  xmlns:v='urn:schemas-microsoft-com:vml'
  xmlns:o='urn:schemas-microsoft-com:office:office'
>
  <head>
    <!--[if gte mso 9]>
      <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG />
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
      </xml>
    <![endif]-->
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta name='x-apple-disable-message-reformatting' />
    <!--[if !mso]><!-->
    <meta http-equiv='X-UA-Compatible' content='IE=edge' />
    <!--<![endif]-->
    <title></title>

    <style type='text/css'>
      @media only screen and (min-width: 520px) {
        .u-row {
          width: 500px !important;
        }

        .u-row .u-col {
          vertical-align: top;
        }

        .u-row .u-col-100 {
          width: 500px !important;
        }
      }

      @media only screen and (max-width: 520px) {
        .u-row-container {
          max-width: 100% !important;
          padding-left: 0px !important;
          padding-right: 0px !important;
        }

        .u-row {
          width: 100% !important;
        }

        .u-row .u-col {
          display: block !important;
          width: 100% !important;
          min-width: 320px !important;
          max-width: 100% !important;
        }

        .u-row .u-col > div {
          margin: 0 auto;
        }

        .u-row .u-col img {
          max-width: 100% !important;
        }
      }

      body {
        margin: 0;
        padding: 0;
      }
      table,
      td,
      tr {
        border-collapse: collapse;
        vertical-align: top;
      }
      p {
        margin: 0;
      }
      .ie-container table,
      .mso-container table {
        table-layout: fixed;
      }
      * {
        line-height: inherit;
      }
      a[x-apple-data-detectors='true'] {
        color: inherit !important;
        text-decoration: none !important;
      }

      table,
      td {
        color: #000000;
      }
      #u_body a {
        color: #0000ee;
        text-decoration: underline;
      }
      @media (max-width: 480px) {
        #u_content_image_1 .v-src-width {
          width: auto !important;
        }
        #u_content_image_1 .v-src-max-width {
          max-width: 63% !important;
        }
      }
    </style>
  </head>

  <body
    class='clean-body u_body'
    style='
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: 100%;
      background-color: #ffffff;
      color: #000000;
    '
  >
    <!--[if IE]><div class='ie-container'><![endif]-->
    <!--[if mso]><div class='mso-container'><![endif]-->
    <table
      id='u_body'
      style='
        border-collapse: collapse;
        table-layout: fixed;
        border-spacing: 0;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        vertical-align: top;
        min-width: 320px;
        margin: 0 auto;
        background-color: #ffffff;
        width: 100%;
      '
      cellpadding='0'
      cellspacing='0'
    >
      <tbody>
        <tr style='vertical-align: top'>
          <td
            style='
              word-break: break-word;
              border-collapse: collapse !important;
              vertical-align: top;
            '
          >
            <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td align='center' style='background-color: #ffffff;'><![endif]-->

            <div
              class='u-row-container'
              style='padding: 0px; background-color: #001a38'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: #001a38;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: #001a38;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: #001a38;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #001a38;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #001a38;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          id='u_content_image_1'
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <table
                                  width='100%'
                                  cellpadding='0'
                                  cellspacing='0'
                                  border='0'
                                >
                                  <tr>
                                    <td
                                      style='
                                        padding-right: 0px;
                                        padding-left: 0px;
                                      '
                                      align='center'
                                    >
                                      <img
                                        align='center'
                                        border='0'
                                        src='https://app.damnmodz.com/images/system/damnmodz-logo.png'
                                        alt=''
                                        title=''
                                        style='
                                          outline: none;
                                          text-decoration: none;
                                          -ms-interpolation-mode: bicubic;
                                          clear: both;
                                          display: inline-block !important;
                                          border: none;
                                          height: auto;
                                          float: none;
                                          width: 100%;
                                          max-width: 236px;
                                        '
                                        width='236'
                                        class='v-src-width v-src-max-width'
                                      />
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 38px 10px 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>Hello $name</p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <div
              class='u-row-container'
              style='padding: 0px; background-color: transparent'
            >
              <div
                class='u-row'
                style='
                  margin: 0 auto;
                  min-width: 320px;
                  max-width: 500px;
                  overflow-wrap: break-word;
                  word-wrap: break-word;
                  word-break: break-word;
                  background-color: transparent;
                '
              >
                <div
                  style='
                    border-collapse: collapse;
                    display: table;
                    width: 100%;
                    height: 100%;
                    background-color: transparent;
                  '
                >
                  <!--[if (mso)|(IE)]><table width='100%' cellpadding='0' cellspacing='0' border='0'><tr><td style='padding: 0px;background-color: transparent;' align='center'><table cellpadding='0' cellspacing='0' border='0' style='width:500px;'><tr style='background-color: transparent;'><![endif]-->

                  <!--[if (mso)|(IE)]><td align='center' width='500' style='background-color: #ffffff;width: 500px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;' valign='top'><![endif]-->
                  <div
                    class='u-col u-col-100'
                    style='
                      max-width: 320px;
                      min-width: 500px;
                      display: table-cell;
                      vertical-align: top;
                    '
                  >
                    <div
                      style='
                        background-color: #ffffff;
                        height: 100%;
                        width: 100% !important;
                        border-radius: 0px;
                        -webkit-border-radius: 0px;
                        -moz-border-radius: 0px;
                      '
                    >
                      <!--[if (!mso)&(!IE)]><!--><div
                        style='
                          box-sizing: border-box;
                          height: 100%;
                          padding: 0px;
                          border-top: 0px solid transparent;
                          border-left: 0px solid transparent;
                          border-right: 0px solid transparent;
                          border-bottom: 0px solid transparent;
                          border-radius: 0px;
                          -webkit-border-radius: 0px;
                          -moz-border-radius: 0px;
                        '
                      ><!--<![endif]-->
                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: left;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p style='line-height: 140%'>
                                    Just to let you know, your order has been completed. You may receive a quick request from Trustpilot to leave a review—it only takes 2 minutes and would greatly help us build trust and grow our business. If you have any questions or need assistance, reply to this email and we’ll be happy to help!
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <table
                          style='font-family: arial, helvetica, sans-serif'
                          role='presentation'
                          cellpadding='0'
                          cellspacing='0'
                          width='100%'
                          border='0'
                        >
                          <tbody>
                            <tr>
                              <td
                                style='
                                  overflow-wrap: break-word;
                                  word-break: break-word;
                                  padding: 10px;
                                  font-family: arial, helvetica, sans-serif;
                                '
                                align='left'
                              >
                                <div
                                  style='
                                    font-size: 14px;
                                    line-height: 140%;
                                    text-align: center;
                                    word-wrap: break-word;
                                  '
                                >
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Experiencing issues with an order?
                                    </span>
                                  </p>
                                  <p
                                    style='
                                      line-height: 140%;
                                      text-align: center;
                                    '
                                  >
                                    <span
                                      style='font-size: 10px; line-height: 14px'
                                      >Send us  a mail
                                      <a
                                        rel='noopener'
                                        href='mailto:admin@damnmodz.com?subject=Support'
                                        target='_blank'
                                        >admin@damnmodz.com</a
                                      ></span
                                    >
                                  </p>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                      </div>
                      <!--<![endif]-->
                    </div>
                  </div>
                  <!--[if (mso)|(IE)]></td><![endif]-->
                  <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
              </div>
            </div>

            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
    <!--[if mso]></div><![endif]-->
    <!--[if IE]></div><![endif]-->
  </body>
</html>

";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function webhook($id, $message)
{
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = HOST_USERNAME;                     //SMTP username
    $mail->Password   = HOST_PASSWORD;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom(HOST_USERNAME, 'DamnModz');
    $mail->addAddress('clottware@gmail.com');     //Add a recipient

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = "DamnModz - Weebhook";
    $mail->Body = "<html>
    <head>
    <title>DamnModz Delivery</title>
    </head>
    <body>
   Product #$id = $message;
    </body>
    </html> ";

    if ($mail->send()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

