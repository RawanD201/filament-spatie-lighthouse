<?php

namespace FilamentSpatieLighthouse\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuditCompletedEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, int|null>  $scores
     */
    public function __construct(
        public string $url,
        public array $scores,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $fmt = fn (?int $score) => $score !== null ? "{$score}/100" : 'N/A';

        return (new MailMessage)
            ->subject('Lighthouse Audit Completed: ' . $this->url)
            ->line('A Lighthouse audit has completed successfully.')
            ->line('**URL:** ' . $this->url)
            ->line('**Performance:** ' . $fmt($this->scores['performance'] ?? null))
            ->line('**Accessibility:** ' . $fmt($this->scores['accessibility'] ?? null))
            ->line('**Best Practices:** ' . $fmt($this->scores['best-practices'] ?? null))
            ->line('**SEO:** ' . $fmt($this->scores['seo'] ?? null))
            ->line('**Time:** ' . now()->toDateTimeString())
            ->action('View Audit Results', url('/admin/lighthouse-results'));
    }
}
