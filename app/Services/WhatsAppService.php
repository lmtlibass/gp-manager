<?php

namespace App\Services;

use App\Enums\NotificationChannel;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Twilio\Rest\Client;

class WhatsAppService
{
    private Client $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token'),
        );
    }

    /**
     * Send a message via WhatsApp or SMS.
     * Returns the Twilio message SID on success, throws on failure.
     */
    public function send(NotificationChannel $channel, string $to, string $body): string
    {
        [$from, $toFormatted] = match ($channel) {
            NotificationChannel::WhatsApp => [
                self::whatsappPrefix(config('services.twilio.whatsapp_from')),
                self::whatsappPrefix($to),
            ],
            NotificationChannel::SMS => [
                config('services.twilio.sms_from'),
                $to,
            ],
        };

        $message = $this->twilio->messages->create($toFormatted, [
            'from' => $from,
            'body' => $body,
        ]);

        return $message->sid;
    }

    private static function whatsappPrefix(string $number): string
    {
        // Strip existing prefix to work on the raw number
        $raw = str_starts_with($number, 'whatsapp:') ? substr($number, 9) : $number;

        if (! str_starts_with($raw, '+')) {
            Log::warning('WhatsApp: numéro sans indicatif pays (+)', ['number' => $raw]);
            throw new InvalidArgumentException("Le numéro « {$raw} » doit commencer par + suivi de l'indicatif pays (ex: +221782902512).");
        }

        return 'whatsapp:' . $raw;
    }
}
