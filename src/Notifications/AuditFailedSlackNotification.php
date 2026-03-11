<?php

namespace FilamentSpatieLighthouse\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Throwable;

class AuditFailedSlackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $url,
        public Throwable $exception,
    ) {
    }

    public function via($notifiable): array
    {
        return ['slack'];
    }

    public function toSlack($notifiable): SlackMessage
    {
        $channel = config('filament-spatie-lighthouse.notifications.slack.channel');

        return (new SlackMessage)
            ->error()
            ->when($channel, fn ($message) => $message->to($channel))
            ->content('Lighthouse Audit Failed')
            ->attachment(function ($attachment) {
                $attachment
                    ->title('Audit Details', url('/admin/lighthouse-results'))
                    ->fields([
                        'URL' => $this->url,
                        'Error' => $this->exception->getMessage(),
                        'Time' => now()->toDateTimeString(),
                    ])
                    ->footer('Lighthouse Plugin');
            });
    }
}
