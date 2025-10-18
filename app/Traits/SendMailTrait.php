<?php

namespace App\Traits;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

trait SendMailTrait
{
    public function sendEmail($receiver, $subject, $content)
    {
        $mail = new PHPMailer(true);

        try {
            // Log mail config for debugging
            Log::info('Mail Config', [
                'host' => config('mail.mailers.smtp.host'),
                'username' => config('mail.mailers.smtp.username'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from' => config('mail.from.address'),
            ]);

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = config('mail.mailers.smtp.host');
            $mail->SMTPAuth   = true;
            $mail->Username   = config('mail.mailers.smtp.username');
            $mail->Password   = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption') ?: PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = config('mail.mailers.smtp.port');

            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));
            $mail->addAddress($receiver);
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $content;

            $mail->send();

            Log::info("Email successfully sent to {$receiver}");

            return ['status' => 200, 'message' => 'Mail sent successfully'];
        } catch (Exception $e) {
            // Log PHPMailer error
            Log::error('PHPMailer failed: ' . $e->getMessage());

            // Try fallback using PHP mail()
            try {
                $headers = "From: " . config('mail.from.name') . " <" . config('mail.from.address') . ">\r\n";
                $headers .= "Reply-To: " . config('mail.from.address') . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                if (mail($receiver, $subject, $content, $headers)) {
                    Log::info("Fallback mail() sent successfully to {$receiver}");
                    return ['status' => 200, 'message' => 'Fallback mail sent successfully'];
                } else {
                    Log::error("Fallback mail() also failed to send to {$receiver}");
                    return ['status' => 500, 'error' => 'Both SMTP and fallback mail() failed'];
                }
            } catch (\Throwable $th) {
                Log::error('Fallback mail() exception: ' . $th->getMessage());
                return ['status' => 500, 'error' => $th->getMessage()];
            }
        }
    }
}
