<?php

namespace FilamentSpatieLighthouse\Console;

use FilamentSpatieLighthouse\Jobs\RunLighthouseAuditJob;
use Illuminate\Console\Scheduling\Schedule;

/**
 * Registers scheduled Lighthouse audits onto the application's scheduler.
 * Called from FilamentSpatieLighthouseServiceProvider::packageBooted().
 */
class Kernel
{
    public function schedule(Schedule $schedule): void
    {
        if (! config('filament-spatie-lighthouse.scheduling.enabled', true)) {
            return;
        }

        $scheduledUrls = config('filament-spatie-lighthouse.scheduling.urls', []);

        foreach ($scheduledUrls as $urlConfig) {
            $url = $urlConfig['url'] ?? null;

            if (! $url) {
                continue;
            }

            $frequency = $urlConfig['frequency'] ?? config('filament-spatie-lighthouse.scheduling.default_frequency', 'daily');
            $categories = $urlConfig['categories'] ?? config('filament-spatie-lighthouse.default_categories', []);
            $formFactor = $urlConfig['form_factor'] ?? 'desktop';

            $schedule->call(function () use ($url, $categories, $formFactor) {
                $job = new RunLighthouseAuditJob(
                    url: $url,
                    categories: $categories,
                    formFactor: $formFactor,
                );

                $queueConnection = config('filament-spatie-lighthouse.queue_connection');
                $queueName = config('filament-spatie-lighthouse.queue_name', 'default');

                if ($queueConnection) {
                    $job->onConnection($queueConnection);
                }

                if ($queueName !== 'default') {
                    $job->onQueue($queueName);
                }

                dispatch($job);
            })->name("lighthouse-audit-{$url}")->{$frequency}();
        }
    }
}
