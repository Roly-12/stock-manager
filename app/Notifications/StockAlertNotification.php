<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification
{
    public function toDatabase(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'message' => "Rupture imminente pour {$this->product->name} (prévue dans {$this->days} jours)",
        ];
    }
}
