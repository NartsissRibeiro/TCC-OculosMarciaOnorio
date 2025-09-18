<?php
require __DIR__ . '/../vendor/autoload.php'; // Autoload do Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController {
    public static function sendMail($to, $subject, $body) {
        // Carregar o arquivo de configuração
        $config = require __DIR__ . '/../config.php';

        // Criar o objeto PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host = $config['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['MAIL_USERNAME'];
            $mail->Password = $config['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['MAIL_PORT'];

            // Definir a codificação do e-mail para UTF-8
            $mail->CharSet = 'UTF-8';

            // Remetente
            $mail->setFrom($config['MAIL_USERNAME'], $config['MAIL_FROM_NAME']);

            // Destinatário
            if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($to);
            } else {
                throw new Exception('Endereço de e-mail inválido.');
            }

            // Conteúdo do e-mail
            $mail->isHTML(true); // Definir que o conteúdo é em HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Enviar o e-mail
            $mail->send();
        } catch (Exception $e) {
            echo "O e-mail não pôde ser enviado. Erro: {$mail->ErrorInfo}";
        }
    }
}
?>