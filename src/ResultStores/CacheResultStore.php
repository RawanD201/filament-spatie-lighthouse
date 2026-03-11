<?php

namespace FilamentSpatieLighthouse\ResultStores;

use FilamentSpatieLighthouse\ResultStores\StoredAuditResults\StoredAuditResults;
use Illuminate\Support\Facades\Cache;
use Spatie\Lighthouse\LighthouseResult;

class CacheResultStore implements ResultStore
{
    public function __construct(
        protected int $cacheTtl = 86400, // 24 hours
    ) {
    }

    public function save(string $url, LighthouseResult $result): void
    {
        $cacheKey = $this->getCacheKey($url);

        Cache::put($cacheKey, [
            'url' => $url,
            'raw_results' => $result->rawResults(),
            'scores' => $result->scores(),
            'finished_at' => now()->toIso8601String(),
        ], now()->addSeconds($this->cacheTtl));
    }

    public function latestResults(?string $url = null): ?StoredAuditResults
    {
        if (! $url) {
            return null;
        }

        $cacheKey = $this->getCacheKey($url);
        $cached = Cache::get($cacheKey);

        if (! $cached) {
            return null;
        }

        return new StoredAuditResults(
            url: $cached['url'],
            finishedAt: \Carbon\Carbon::parse($cached['finished_at']),
            rawResults: $cached['raw_results'],
            scores: $cached['scores'],
        );
    }

    public function getHistory(?string $url = null, int $limit = 10): array
    {
        // Cache store doesn't support history, return latest only
        $latest = $this->latestResults($url);

        if (! $latest) {
            return [];
        }

        return [[
            'url' => $latest->url,
            'scores' => $latest->scores,
            'finished_at' => $latest->finishedAt,
        ]];
    }

    protected function getCacheKey(string $url): string
    {
        return 'lighthouse_result_' . md5($url);
    }
}
