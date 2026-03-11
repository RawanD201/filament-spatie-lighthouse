<?php

namespace FilamentSpatieLighthouse\ResultStores\StoredAuditResults;

use Carbon\Carbon;

class StoredAuditResults
{
    /**
     * @param  array<string, mixed>  $rawResults
     * @param  array<string, mixed>  $scores
     */
    public function __construct(
        public string $url,
        public ?Carbon $finishedAt,
        public array $rawResults = [],
        public array $scores = [],
        public ?string $rawResultPath = null,
    ) {
    }

    public function performanceScore(): ?int
    {
        return isset($this->scores['performance']) ? (int) $this->scores['performance'] : null;
    }

    public function accessibilityScore(): ?int
    {
        return isset($this->scores['accessibility']) ? (int) $this->scores['accessibility'] : null;
    }

    public function bestPracticesScore(): ?int
    {
        return isset($this->scores['best-practices']) ? (int) $this->scores['best-practices'] : null;
    }

    public function seoScore(): ?int
    {
        return isset($this->scores['seo']) ? (int) $this->scores['seo'] : null;
    }
}
