<?php

use PHPMailer\PHPMailer\{PHPMailer, SMTP, Exception};

class Mailer
{
    function enviarEmail($email, $asunto, $cuerpo)
    {
        //Load Composer's autoloader
        require 'vendor/autoload.php';

        //instancia de la clase php mailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF; //para evitar imprimir todo el debug
            $mail->isSMTP();                        //enviar usando SMTP
            $mail->Host       = 'smtp.gmail.com';        //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                      //Enable SMTP authentication
            $mail->Username   = 'gladius3312@gmail.com';   //SMTP username
            $mail->Password   = 'mhgppovrrqxrrcas';           //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    //Enable implicit TLS encryption
            $mail->Port       = 465;                 //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients (direccion de correo desde donde se enviaran los correos)
            //correo emisor y nombre
            $mail->setFrom('gladius3312@gmail.com', 'Olympus');
            //correo receptor y nombre
            $mail->addAddress($email, 'Usuario Olympus');     //Add a recipient(el que recibe el correo,'titulo')

            //Contenido
            $mail->isHTML(true); // establecer el formato de correo electronico en HTML
            $mail->CharSet = 'UTF-8'; //sin esto la letra 'Ñ' no se ve
            $mail->Subject = $asunto; //Titulo del correo

            //Cuerpo del correo
            $mail->Body = $cuerpo;

            //Enviar correo
            if ($mail->send()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "No se pudo enviar el mensaje. Error de envio:{$mail->ErrorInfo}"
            ]);
            return false;
        }
    }
}
