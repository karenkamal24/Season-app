<?php

namespace App\Mail;

use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Log;

class SendGridMail
{
    public static function send($to, $subject, $body)
    {
        $email = new Mail();
        
        try {
            $email->setFrom(
                config('mail.from.address'),
                config('mail.from.name')
            );
            
            $email->setSubject($subject);
            $email->addTo($to);
            $email->addContent("text/html", $body);
            
            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
            $response = $sendgrid->send($email);
            
            Log::info('✅ SendGrid API Success', [
                'status' => $response->statusCode(),
                'to' => $to,
                'subject' => $subject
            ]);
            
            return [
                'status' => $response->statusCode(),
                'success' => true
            ];
            
        } catch (\Exception $e) {
            Log::error('❌ SendGrid API Error', [
                'error' => $e->getMessage(),
                'to' => $to
            ]);
            
            throw $e;
        }
    }
}