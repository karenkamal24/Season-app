<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendGridMail
{
    public static function send($to, $subject, $body)
    {
        try {
            $response = Http::withToken(config('services.resend.key'))
                ->acceptJson()
                ->post('https://api.resend.com/emails', [
                    'from' => config('mail.from.address'),
                    'to' => [$to],
                    'subject' => $subject,
                    'html' => $body,
                ]);

            if ($response->failed()) {
                Log::error('❌ Resend API Error', [
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                    'to' => $to,
                ]);

                throw new \RuntimeException('Resend API request failed.');
            }

            Log::info('✅ Resend API Success', [
                'status' => $response->status(),
                'to' => $to,
                'subject' => $subject,
                'id' => $response->json('id'),
            ]);

            return [
                'status' => $response->status(),
                'success' => true,
                'id' => $response->json('id'),
            ];

        } catch (\Exception $e) {
            Log::error('❌ Resend API Error', [
                'error' => $e->getMessage(),
                'to' => $to
            ]);

            throw $e;
        }
    }
}
