<?php

namespace App\Notifications;

use App\Models\Alerte;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class AlerteNotification extends Notification
{
    use Queueable;

    public function __construct(protected Alerte $alerte)
    {
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $frontendUrl = rtrim(config('app.frontend_url', 'https://www.2i-online.com'), '/');
        $url = $this->alerte->live_session_id
            ? "{$frontendUrl}/cours/{$this->alerte->formation_id}/direct/{$this->alerte->live_session_id}"
            : "{$frontendUrl}/cours/{$this->alerte->formation_id}";

        return (new WebPushMessage())
            ->title($this->alerte->titre)
            ->icon('/favicon.ico')
            ->body($this->alerte->message)
            ->data(['url' => $url])
            ->options(['TTL' => 3600]);
    }
}
