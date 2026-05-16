<?php

namespace App\Services;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Enums\PackageStatus;
use App\Models\Package;
use App\Models\PackageNotification;
use Illuminate\Support\Facades\Log;
use Throwable;

class PackageNotifier
{
    public function __construct(private WhatsAppService $whatsapp) {}

    public function notifyCreated(Package $package): void
    {
        $tracking = $package->tracking_number;

        $this->dispatch(
            $package,
            $package->sender?->phone,
            "Votre colis *{$tracking}* a bien été enregistré et sera acheminé sous peu. Merci de votre confiance."
        );

        $this->dispatch(
            $package,
            $package->receiver?->phone,
            "Un colis *{$tracking}* vous est destiné. Votre code de retrait : *{$package->withdrawal_code}*. Vous serez notifié à son arrivée."
        );
    }

    public function notifyStatusChanged(Package $package, string $oldStatus): void
    {
        if ($oldStatus === $package->status->value) {
            return;
        }

        $tracking = $package->tracking_number;
        $status = $package->status;

        [$senderMsg, $receiverMsg] = match ($status) {
            PackageStatus::InTransit => [
                "Votre colis *{$tracking}* est en transit vers l'agence de destination.",
                "Un colis *{$tracking}* qui vous est destiné est en transit.",
            ],
            PackageStatus::Arrived => [
                "Votre colis *{$tracking}* est arrivé à l'agence de destination.",
                "Votre colis *{$tracking}* est arrivé à l'agence. Il sera disponible au retrait sous peu.",
            ],
            PackageStatus::ReadyForPickup => [
                "Votre colis *{$tracking}* est prêt pour le retrait.",
                "Votre colis *{$tracking}* est prêt ! Présentez votre code de retrait : *{$package->withdrawal_code}*.",
            ],
            PackageStatus::Delivered => [
                "Votre colis *{$tracking}* a été livré avec succès. Merci !",
                "Votre colis *{$tracking}* vous a été remis. Merci d'avoir utilisé nos services.",
            ],
            PackageStatus::Cancelled => [
                "Votre colis *{$tracking}* a été annulé. Contactez-nous pour plus d'informations.",
                "Le colis *{$tracking}* qui vous était destiné a été annulé.",
            ],
            default => [null, null],
        };

        if ($senderMsg) {
            $this->dispatch($package, $package->sender?->phone, $senderMsg);
        }
        if ($receiverMsg) {
            $this->dispatch($package, $package->receiver?->phone, $receiverMsg);
        }
    }

    private function dispatch(Package $package, ?string $phone, string $message): void
    {
        if (! $phone) {
            return;
        }

        $channel = NotificationChannel::WhatsApp;
        $status = NotificationStatus::Pending;
        $failedReason = null;
        $sentAt = null;

        try {
            $this->whatsapp->send($channel, $phone, $message);
            $status = NotificationStatus::Sent;
            $sentAt = now();
            Log::info('WhatsApp envoyé', ['to' => $phone, 'package' => $package->tracking_number]);
        } catch (Throwable $e) {
            $status = NotificationStatus::Failed;
            $failedReason = $e->getMessage();
            Log::error('WhatsApp échec', [
                'to'      => $phone,
                'package' => $package->tracking_number,
                'error'   => $e->getMessage(),
            ]);
        }

        PackageNotification::query()->create([
            'package_id'      => $package->id,
            'channel'         => $channel,
            'recipient_phone' => $phone,
            'message'         => $message,
            'status'          => $status,
            'sent_at'         => $sentAt,
            'failed_reason'   => $failedReason,
        ]);
    }
}
