<?php

namespace FilamentSpatieLighthouse\Console;

use FilamentSpatieLighthouse\ResultStores\ResultStore;
use Illuminate\Console\Command;
use Spatie\Lighthouse\Lighthouse;

class RunLighthouseAuditCommand extends Command
{
    protected $signature = 'lighthouse:audit {url} {--timeout=180}';

    protected $description = 'Run a Lighthouse audit on a URL';

    public function handle(ResultStore $resultStore): int
    {
        $url = $this->argument('url');
        $timeout = (int) $this->option('timeout');

        $this->info("Running Lighthouse audit on: {$url}");

        try {
            $result = Lighthouse::url($url)
                ->timeoutInSeconds($timeout)
                ->run();

            $resultStore->save($url, $result);

            $scores = $result->scores();

            $this->info('Audit completed successfully!');
            $this->newLine();
            $this->info('Scores:');
            $this->table(
                ['Category', 'Score'],
                [
                    ['Performance', $scores['performance'] ?? 'N/A'],
                    ['Accessibility', $scores['accessibility'] ?? 'N/A'],
                    ['Best Practices', $scores['best-practices'] ?? 'N/A'],
                    ['SEO', $scores['seo'] ?? 'N/A'],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Audit failed: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
