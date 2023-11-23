<?php

use PHPMailer\PHPMailer\{PHPMailer, SMTP, Exception};

//Load Composer's autoloader
require 'vendor/autoload.php';

//instancia de la clase php mailer
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //para enviar mensajes de debug al momento de enviar el correo
    $mail->isSMTP();                                            //enviar usando SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'juan12220785@gmail.com';                     //SMTP username
    $mail->Password   = '';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients (direccion de correo desde donde se enviaran los correos)
    $mail->setFrom('juan12220785@gmail.com', 'Mailer');
    $mail->addAddress('w.olf@live.com.ar', 'Joe User');     //Add a recipient(el que recibe el correo,'titulo')
    $mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');   //con copia a otra direccion
    $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML

    $mail->Subject = 'Recuperar contraseña - Punto de venta Olympus';

    $mail->Body    = "Estimado $nombre : <br> Si has solicitado el cambio de tu contraseña
    da clic en el siguiente link: <br>
    <a href='$url'>$url</a>  <br>
    Si no hiciste esta solicitud puedes ignorar este correo.";

    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
