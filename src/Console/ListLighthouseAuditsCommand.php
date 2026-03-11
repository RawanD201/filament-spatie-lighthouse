<?php

namespace FilamentSpatieLighthouse\Console;

use FilamentSpatieLighthouse\Models\LighthouseAuditResult;
use Illuminate\Console\Command;

class ListLighthouseAuditsCommand extends Command
{
    protected $signature = 'lighthouse:list 
                            {--url= : Filter by URL}
                            {--limit=10 : Number of results to show}
                            {--format=table : Output format (table, json, csv)}';

    protected $description = 'List recent Lighthouse audit results';

    public function handle(): int
    {
        $url = $this->option('url');
        $limit = (int) $this->option('limit');
        $format = $this->option('format');

        $query = LighthouseAuditResult::query()
            ->when($url, fn($q) => $q->where('url', $url))
            ->latest('finished_at')
            ->limit($limit);

        $results = $query->get();

        if ($results->isEmpty()) {
            $this->warn('No audit results found.');
            return Command::SUCCESS;
        }

        match ($format) {
            'json' => $this->outputJson($results),
            'csv' => $this->outputCsv($results),
            default => $this->outputTable($results),
        };

        return Command::SUCCESS;
    }

    protected function outputTable($results): void
    {
        $headers = ['ID', 'URL', 'Performance', 'Accessibility', 'Best Practices', 'SEO', 'Finished At'];
        $rows = $results->map(function ($result) {
            return [
                $result->id,
                $result->url,
                $result->performance_score ?? 'N/A',
                $result->accessibility_score ?? 'N/A',
                $result->best_practices_score ?? 'N/A',
                $result->seo_score ?? 'N/A',
                $result->finished_at?->toDateTimeString() ?? 'N/A',
            ];
        })->toArray();

        $this->table($headers, $rows);
    }

    protected function outputJson($results): void
    {
        $data = $results->map(function ($result) {
            return [
                'id' => $result->id,
                'url' => $result->url,
                'performance_score' => $result->performance_score,
                'accessibility_score' => $result->accessibility_score,
                'best_practices_score' => $result->best_practices_score,
                'seo_score' => $result->seo_score,
                'finished_at' => $result->finished_at?->toIso8601String(),
            ];
        })->toArray();

        $this->line(json_encode($data, JSON_PRETTY_PRINT));
    }

    protected function outputCsv($results): void
    {
        $handle = fopen('php://output', 'w');

        fputcsv($handle, ['ID', 'URL', 'Performance', 'Accessibility', 'Best Practices', 'SEO', 'Finished At']);

        foreach ($results as $result) {
            fputcsv($handle, [
                $result->id,
                $result->url,
                $result->performance_score ?? 'N/A',
                $result->accessibility_score ?? 'N/A',
                $result->best_practices_score ?? 'N/A',
                $result->seo_score ?? 'N/A',
                $result->finished_at?->toDateTimeString() ?? 'N/A',
            ]);
        }

        fclose($handle);
    }
}
