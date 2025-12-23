<?php

namespace App\Traits;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

trait SendMailTrait
{
    public function sendEmail($receiver, $subject, $content)
    {
        $mail = new PHPMailer(true);

        try {
            // Decide SMTP credentials (SendGrid or default SMTP)
            $sendgridKey = env('SENDGRID_API_KEY');
            $mailHost = config('mail.mailers.smtp.host');
            $isSendGrid = $sendgridKey || (str_contains($mailHost ?? '', 'sendgrid.net'));

            $host = $isSendGrid ? 'smtp.sendgrid.net' : $mailHost;
            $username = $isSendGrid ? 'apikey' : config('mail.mailers.smtp.username');
            $password = $sendgridKey ?: config('mail.mailers.smtp.password');
            $port = $isSendGrid ? 587 : config('mail.mailers.smtp.port');

            // Handle encryption: convert 'tls'/'ssl' string to PHPMailer constants
            $encryptionConfig = config('mail.mailers.smtp.encryption', 'tls');
            if ($isSendGrid) {
                $encryption = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($encryptionConfig === 'tls') {
                $encryption = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($encryptionConfig === 'ssl') {
                $encryption = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $encryption = PHPMailer::ENCRYPTION_STARTTLS;
            }

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $username;
            $mail->Password   = $password;
            $mail->SMTPSecure = $encryption;
            $mail->Port       = $port;
            $mail->Timeout    = 10;
            $mail->SMTPKeepAlive = true;
            $mail->SMTPDebug = 0; // Disable SMTP debug output

            $fromAddress = config('mail.from.address');
            $fromName = config('mail.from.name');

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($receiver);
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $content;

            $sendResult = $mail->send();

            if ($sendResult) {
                return ['status' => 200, 'message' => 'Mail sent successfully'];
            } else {
                return ['status' => 500, 'error' => 'Mail send() returned false: ' . ($mail->ErrorInfo ?? 'Unknown error')];
            }
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
                'phpmailer_error' => $mail->ErrorInfo ?? 'No error info',
                'smtp_config' => [
                    'host' => $host ?? 'N/A',
                    'port' => $port ?? 'N/A',
                    'username' => $username ?? 'N/A',
                    'is_sendgrid' => $isSendGrid ?? false,
                ],
            ];
        }
    }
}
