<?php

namespace FilamentSpatieLighthouse\Listeners;

use FilamentSpatieLighthouse\Events\AuditFailedEvent;
use FilamentSpatieLighthouse\Notifications\AuditFailedEmailNotification;
use FilamentSpatieLighthouse\Notifications\AuditFailedSlackNotification;
use Illuminate\Support\Facades\Notification;

class SendAuditFailedNotifications
{
    public function handle(AuditFailedEvent $event): void
    {
        $config = config('filament-spatie-lighthouse.notifications', []);

        // Send email notifications if enabled
        if (($config['email']['enabled'] ?? false) && ($config['email']['on_failure'] ?? true)) {
            $emails = $this->getEmailRecipients();
            if (!empty($emails)) {
                Notification::route('mail', $emails)
                    ->notify(new AuditFailedEmailNotification(
                        url: $event->url,
                        exception: $event->exception,
                    ));
            }
        }

        // Send Slack notifications if enabled
        if (($config['slack']['enabled'] ?? false) && ($config['slack']['on_failure'] ?? true)) {
            $webhookUrl = $config['slack']['webhook_url'] ?? null;
            if ($webhookUrl) {
                // Create a notifiable instance for Slack
                $slackNotifiable = new class {
                    public function routeNotificationForSlack()
                    {
                        return config('filament-spatie-lighthouse.notifications.slack.webhook_url');
                    }
                };

                Notification::send($slackNotifiable, new AuditFailedSlackNotification(
                    url: $event->url,
                    exception: $event->exception,
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
