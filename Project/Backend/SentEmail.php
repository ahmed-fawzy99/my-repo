<?php 
// Import PHPMailer classes into the global namespace 
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\SMTP; 
use PHPMailer\PHPMailer\Exception; 
 
// Include library files 
require 'PHPMailer/Exception.php'; 
require 'PHPMailer/PHPMailer.php'; 
require 'PHPMailer/SMTP.php'; 
include 'domain-name.php';

// Create an instance; Pass `true` to enable exceptions 
$mail = new PHPMailer; 
 
// Server settings 
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;        //Enable verbose debug output 
$mail->isSMTP();                                // Set mailer to use SMTP 
$mail->Host = 'smtp-mail.outlook.com';          // Specify main and backup SMTP servers 
$mail->SMTPAuth = true;                         // Enable SMTP authentication 
$mail->Username = 'MyRepo99@hotmail.com';       // SMTP username 
$mail->Password = '***********';             // SMTP password 
$mail->SMTPSecure = 'STARTTLS';                 // Enable TLS encryption, `ssl` also accepted 
$mail->Port = 587;                              // TCP port to connect to 
 
// Sender info 
$mail->setFrom('MyRepo99@hotmail.com', 'MyRepoTeam'); 

 
if($_SERVER['REQUEST_METHOD']== "POST"){


// Add a recipient 
$usermail=$_POST['email'];

//Encrypting_user_email
$ciphering = "AES-128-CTR";
$options = 0;
$iv = '1234235491011121';
$key = "3k83RaZkn7Ls";
$enc_user_email = openssl_encrypt($usermail, $ciphering, $key, $options, $iv);

$mail->addAddress($usermail); 
 
// Set email format to HTML 
$mail->isHTML(true); 
 
// Mail subject 
$mail->Subject = 'Email from MyRepo'; 
 
// Mail body content 
$bodyContent = '<h1>Password Reset</h1>'; 
$bodyContent .= '<p>You are receiving this email because you requested a password reset at www.myrepo.com
                Please click on the following link to reset your password: <a href="'.$DOMAIN_NAME.'/getReset.php?'.$enc_user_email.'">Reset password</a> to reset password.
                If this was not you, please report the admin immediately.</p>'; 
$mail->Body    = $bodyContent; 
 
// Send email 
    if(!$mail->send()) { 
        echo 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo; 
    } else { 
        $message = "Email has been sent";
        echo "<script type='text/javascript'>
        alert('$message');
        location.href = '$DOMAIN_NAME/loginpage.php';
        </script>"; 
    }
}
else{
    echo "nothing been posted";
}
