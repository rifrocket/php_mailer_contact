<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

//Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);
$HOME_PATH = $_SERVER['SERVER_NAME'];   

//SET ENVOIRMENT
if(trim($_ENV['TESTING'])=='true') {

    $MAIL_HOST= trim( $_ENV['MAIL_HOST_TEST']==''?'smtp.mailtrap.io':$_ENV['MAIL_HOST_TEST']);
    $MAIL_PORT= trim( $_ENV['MAIL_PORT_TEST']==''?'2525':$_ENV['MAIL_PORT_TEST']);
    $MAIL_USERNAME= trim( $_ENV['MAIL_USERNAME_TEST']==''?'4be2332fd616ca':$_ENV['MAIL_USERNAME_TEST']);
    $MAIL_PASSWORD= trim( $_ENV['MAIL_PASSWORD_TEST']==''?'72e31ca4de1958':$_ENV['MAIL_PASSWORD_TEST']);
    $MAIL_ENCRYPTION= trim( $_ENV['MAIL_ENCRYPTION_TEST']==''?'ssl':$_ENV['MAIL_ENCRYPTION_TEST']);   
}
else
{
    $MAIL_HOST= trim( $_ENV['MAIL_HOST']);
    $MAIL_PORT= trim( $_ENV['MAIL_PORT']);
    $MAIL_USERNAME= trim( $_ENV['MAIL_USERNAME']);
    $MAIL_PASSWORD= trim( $_ENV['MAIL_PASSWORD']);
    $MAIL_ENCRYPTION= trim( $_ENV['MAIL_ENCRYPTION']);
}

    $MAIL_SENDER= trim( $_ENV['MAIL_SENDER']==''?'example@mail.com':$_ENV['MAIL_SENDER']);
    $MAIL_SUBJECT= trim( $_ENV['MAIL_SUBJECT']==''?'email subject':$_ENV['MAIL_SUBJECT']);
    $MAIL_SENDER_NAME= trim( $_ENV['MAIL_SENDER_NAME']==''?'RifRocket':$_ENV['MAIL_SENDER_NAME']);
    $LOGO_PATH= trim( $_ENV['LOGO_PATH']==''?'/success.png':$_ENV['LOGO_PATH']);
    $EMAIL_TITLE= trim( $_ENV['EMAIL_TITLE']==''?'email title':$_ENV['EMAIL_TITLE']);
    $MESSAGE_INFORMATION= trim( $_ENV['MESSAGE_INFORMATION']==''?'message body':$_ENV['MESSAGE_INFORMATION']);


 //Input Verification function
    function verifyInput($input, $type)
    {
        switch ($type)
        {
            case 0:
                $pattern =  '/^[A-Za-zÁ-Úá-ú0-9àÀÜü\s()\/\'":\*,.;\-!?&#$@]{1,1500}$/';
                break;

            case 1:
                $pattern =  '/^[A-Za-zÁ-Úá-úàÀÜü0-9\']+[A-Za-zÁ-Úá-úàÀÜü0-9 \'\-\.]+$/';
                break;

            case 2:
                $pattern = '/^.+@[^\.].*\.[a-z]{2,}$/';
                break;

            default :
                $pattern = 'default';
                break;
        }

        if ( $pattern !='default' && preg_match($pattern, $input) == 1) {
            return true;
        } else {
            return false;
        }
    }

try {

    //Post inputs
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

        $name=$_POST['name'];
        $email=$_POST['email'];
        $subject=$_POST['subject'];
        $message_body=$_POST['message'];

        //Post inputs velidation name
        if (! verifyInput($name, 1)) {
            $error = ('0|Please fill out your name');
            echo $error;
            die;
        }

        //Post inputs velidation email
        if (! verifyInput($email, 2)) {
            $error = ('0|Please fill out your email');
            echo $error;
            die;
        }

        //Post inputs velidation message_body
        if (! verifyInput($message_body, 0)) {
            $error = '0|Please fill out your message ';
            echo $error;
            die;
        }

    }

    else {
        //If request is not post
        $error = ('0|There is something wrong, please try after sometime');
        echo $error;
        die;
    }
    



    // }
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = $MAIL_HOST;                             // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                 // Enable SMTP authentication
    $mail->SMTPSecure =  $MAIL_ENCRYPTION;                   // Enable ssl authentication
     $mail->SMTPDebug  = 0;                                   // enables SMTP debug information (for testing) 1 = errors and messages  2 = messages only
    $mail->Username   = $MAIL_USERNAME;                     // SMTP username
    $mail->Password   = $MAIL_PASSWORD;                     // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = $MAIL_PORT;                  // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above


    //Recipients
    $mail->setFrom($MAIL_SENDER, $MAIL_SENDER_NAME);
    $mail->addAddress(trim($_ENV['MAIL_RESEAVER']));               // Name is optional

    //More Recipients Options:
    // $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    // Attachments:
    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    //Add email HTML body
    $body = file_get_contents('mailerTemplet.php');

    //View replaceble veriables
    $email_vars=array(
         "SERVER_PATH"=> $HOME_PATH,
        "LOGO_PATH"=>$LOGO_PATH,
        "EMAIL_TITLE"=>$EMAIL_TITLE,
        "MESSAGE_INFORMATION"=>$MESSAGE_INFORMATION,
        "SENDER_NAME"=>$name,
        "SENDER_EMAIL"=>$email,
        "SENDER_SUBJECET"=>$subject,
        "MESSAGE_BODY"=>$message_body,
       

    );
    //Set placeholder  //replcase placehoder in the view with veraible
    foreach ($email_vars as $key => $value) {
        $body = str_replace('{'.strtoupper($key).'}', $value, $body);
    }
    

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $subject==''? $MAIL_SUBJECT :$subject ;
    $mail->Body    =  $body;
    $mail->send();
    $success='1| Message has been sent';
    echo $success;
    die;
} catch (Exception $e) {
    $error= "0|Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    echo $error;
    die;
}
