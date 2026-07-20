<?php
// app/Notifications/AlerteCoursNotification.php

namespace App\Notifications;

use App\Models\Alerte;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class AlerteCoursNotification extends Notification
{
    public function __construct(private Alerte $alerte) {}

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage())
            ->title($this->alerte->titre)
            ->icon('/icons/2ionline-logo.png')
            ->body($this->alerte->message)
            ->data(['cours_id' => $this->alerte->cours_id])
            ->action('Voir le cours', 'voir_cours')
            ->options(['TTL' => 3600]);
    }
}