<?php
require __DIR__ . '/../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController {
    public static function sendMail($to, $subject, $body) {
        $config = require __DIR__ . '/../config.php';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $config['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['MAIL_USERNAME'];
            $mail->Password = $config['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['MAIL_PORT'];

            $mail->CharSet = 'UTF-8';

            $mail->setFrom($config['MAIL_USERNAME'], $config['MAIL_FROM_NAME']);

            if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($to);
            } else {
                throw new Exception('Endereço de e-mail inválido.');
            }

            $mail->isHTML(true); 
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
        } catch (Exception $e) {
            echo "O e-mail não pôde ser enviado. Erro: {$mail->ErrorInfo}";
        }
    }
}
?>