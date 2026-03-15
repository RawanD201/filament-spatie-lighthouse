<?php

namespace FilamentSpatieLighthouse\ResultStores;

use FilamentSpatieLighthouse\Models\LighthouseAuditResult;
use FilamentSpatieLighthouse\ResultStores\StoredAuditResults\StoredAuditResults;
use Illuminate\Support\Facades\Storage;
use Spatie\Lighthouse\LighthouseResult;

class DatabaseResultStore implements ResultStore
{
    public function save(string $url, LighthouseResult $result): void
    {
        $useFilesystem = config('filament-spatie-lighthouse.raw_results_driver') === 'filesystem';
        $rawResults = null;
        $rawResultPath = null;

        if ($useFilesystem) {
            $disk = config('filament-spatie-lighthouse.raw_results_disk', 'local');
            $basePath = config('filament-spatie-lighthouse.raw_results_path', 'lighthouse-results');
            $rawResultPath = $basePath . '/' . md5($url) . '-' . now()->format('Y-m-d-His') . '.json';
            Storage::disk($disk)->put($rawResultPath, json_encode($result->rawResults()));
        } else {
            $rawResults = $result->rawResults();
        }

        LighthouseAuditResult::create([
            'url' => $url,
            'raw_results' => $rawResults,
            'raw_result_path' => $rawResultPath,
            'scores' => $result->scores(),
            'performance_score' => $result->scores('performance'),
            'accessibility_score' => $result->scores('accessibility'),
            'best_practices_score' => $result->scores('best-practices'),
            'seo_score' => $result->scores('seo'),
            'finished_at' => now(),
        ]);
    }

    public function latestResults(?string $url = null): ?StoredAuditResults
    {
        // Use orderByDesc('id') instead of latest() so MySQL can traverse the
        // clustered primary-key index without a sort buffer — avoiding OOM
        // when raw_results rows are large.
        $latestId = LighthouseAuditResult::query()
            ->select('id')
            ->when($url, fn ($q) => $q->where('url', $url))
            ->orderByDesc('id')
            ->value('id');

        if (! $latestId) {
            return null;
        }

        $latest = LighthouseAuditResult::find($latestId);

        if (! $latest) {
            return null;
        }

        return new StoredAuditResults(
            url: $latest->url,
            finishedAt: $latest->finished_at,
            rawResults: $this->resolveRawResults($latest),
            scores: $latest->scores,
            rawResultPath: $latest->raw_result_path,
        );
    }

    protected function resolveRawResults(LighthouseAuditResult $record): array
    {
        if ($record->raw_result_path) {
            $disk = config('filament-spatie-lighthouse.raw_results_disk', 'local');
            $json = Storage::disk($disk)->get($record->raw_result_path);

            return $json ? json_decode($json, true) : [];
        }

        return $record->raw_results ?? [];
    }

    public function getHistory(?string $url = null, int $limit = 10): array
    {
        $query = LighthouseAuditResult::query()
            ->select(['id', 'url', 'scores', 'performance_score', 'accessibility_score', 'best_practices_score', 'seo_score', 'finished_at', 'created_at'])
            ->orderByDesc('id');

        if ($url) {
            $query->where('url', $url);
        }

        return $query->limit($limit)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'url' => $item->url,
                'scores' => $item->scores,
                'performance_score' => $item->performance_score,
                'accessibility_score' => $item->accessibility_score,
                'best_practices_score' => $item->best_practices_score,
                'seo_score' => $item->seo_score,
                'finished_at' => $item->finished_at,
                'created_at' => $item->created_at,
            ];
        })->toArray();
    }
}
