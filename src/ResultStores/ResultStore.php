<?php

namespace FilamentSpatieLighthouse\ResultStores;

use FilamentSpatieLighthouse\ResultStores\StoredAuditResults\StoredAuditResults;
use Spatie\Lighthouse\LighthouseResult;

interface ResultStore
{
    public function save(string $url, LighthouseResult $result): void;

    public function latestResults(?string $url = null): ?StoredAuditResults;

    public function getHistory(?string $url = null, int $limit = 10): array;
}
