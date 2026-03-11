<?php

namespace FilamentSpatieLighthouse\Console;

use FilamentSpatieLighthouse\Jobs\RunLighthouseAuditJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScheduleLighthouseAuditCommand extends Command
{
    protected $signature = 'lighthouse:schedule 
                            {url : The URL to audit}
                            {--categories=* : Categories to audit (performance, accessibility, best-practices, seo)}
                            {--form-factor=desktop : Form factor (desktop or mobile)}
                            {--timeout=180 : Timeout in seconds}';

    protected $description = 'Schedule a Lighthouse audit to run in the queue';

    public function handle(): int
    {
        $url = $this->argument('url');
        $categories = $this->option('categories') ?: config('filament-spatie-lighthouse.default_categories', []);
        $formFactor = $this->option('form-factor');
        $timeout = (int) $this->option('timeout');

        $this->info("Scheduling Lighthouse audit for: {$url}");

        $job = new RunLighthouseAuditJob(
            url: $url,
            categories: $categories,
            formFactor: $formFactor,
            timeoutSeconds: $timeout,
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

        $this->info('Audit job queued successfully!');

        return Command::SUCCESS;
    }
}
