<?php

namespace FilamentSpatieLighthouse\Listeners;

use FilamentSpatieLighthouse\Events\AuditEndedEvent;
use FilamentSpatieLighthouse\Notifications\AuditCompletedEmailNotification;
use FilamentSpatieLighthouse\Notifications\AuditCompletedSlackNotification;
use Illuminate\Support\Facades\Notification;

class SendAuditCompletedNotifications
{
    public function handle(AuditEndedEvent $event): void
    {
        $config = config('filament-spatie-lighthouse.notifications', []);

        // Send email notifications if enabled
        if (($config['email']['enabled'] ?? false) && ($config['email']['on_completion'] ?? false)) {
            $emails = $this->getEmailRecipients();
            if (!empty($emails)) {
                Notification::route('mail', $emails)
                    ->notify(new AuditCompletedEmailNotification(
                        url: $event->url,
                        scores: $event->scores,
                    ));
            }
        }

        // Send Slack notifications if enabled
        if (($config['slack']['enabled'] ?? false) && ($config['slack']['on_completion'] ?? false)) {
            $webhookUrl = $config['slack']['webhook_url'] ?? null;
            if ($webhookUrl) {
                // Create a notifiable instance for Slack
                $slackNotifiable = new class {
                    public function routeNotificationForSlack()
                    {
                        return config('filament-spatie-lighthouse.notifications.slack.webhook_url');
                    }
                };

                Notification::send($slackNotifiable, new AuditCompletedSlackNotification(
                    url: $event->url,
                    scores: $event->scores,
                ));
            }
        }
    }

    protected function getEmailRecipients(): array
    {
        $emails = config('filament-spatie-lighthouse.notifications.email.to');
        
        if (empty($emails)) {
            return [];
        }

        return array_map('trim', explode(',', $emails));
    }
}
