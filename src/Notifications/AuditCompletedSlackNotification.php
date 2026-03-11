<?php

namespace FilamentSpatieLighthouse\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class AuditCompletedSlackNotification extends Notification implements ShouldQueue
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
        return ['slack'];
    }

    public function toSlack($notifiable): SlackMessage
    {
        $channel = config('filament-spatie-lighthouse.notifications.slack.channel');
        $performance = $this->scores['performance'] ?? null;

        $color = match (true) {
            $performance === null => 'warning',
            $performance >= 90    => 'good',
            $performance >= 50    => 'warning',
            default               => 'danger',
        };

        $fmt = fn (?int $score) => $score !== null ? "{$score}/100" : 'N/A';

        return (new SlackMessage)
            ->success()
            ->when($channel, fn ($message) => $message->to($channel))
            ->content('Lighthouse Audit Completed')
            ->attachment(function ($attachment) use ($color, $fmt) {
                $attachment
                    ->title('Audit Results', url('/admin/lighthouse-results'))
                    ->color($color)
                    ->fields([
                        'URL'           => $this->url,
                        'Performance'   => $fmt($this->scores['performance'] ?? null),
                        'Accessibility' => $fmt($this->scores['accessibility'] ?? null),
                        'Best Practices'=> $fmt($this->scores['best-practices'] ?? null),
                        'SEO'           => $fmt($this->scores['seo'] ?? null),
                        'Time'          => now()->toDateTimeString(),
                    ])
                    ->footer('Lighthouse Plugin');
            });
    }
}
