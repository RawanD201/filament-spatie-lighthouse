<?php

namespace FilamentSpatieLighthouse\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Throwable;

class AuditFailedEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $url,
        public Throwable $exception,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Lighthouse Audit Failed: ' . $this->url)
            ->line('A Lighthouse audit has failed.')
            ->line('**URL:** ' . $this->url)
            ->line('**Error:** ' . $this->exception->getMessage())
            ->line('**Time:** ' . now()->toDateTimeString())
            ->when(
                config('filament-spatie-lighthouse.notifications.email.show_stack_trace', false),
                fn ($mail) => $mail->line('**Stack Trace:**')
                    ->line('```' . $this->exception->getTraceAsString() . '```')
            )
            ->action('View Audit Results', url('/admin/lighthouse-results'));
    }
}
