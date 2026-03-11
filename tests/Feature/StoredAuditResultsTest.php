<?php

use FilamentSpatieLighthouse\ResultStores\StoredAuditResults\StoredAuditResults;

it('returns correct individual scores from scores array', function () {
    $stored = new StoredAuditResults(
        url: 'https://example.com',
        finishedAt: now(),
        rawResults: [],
        scores: [
            'performance'   => 92,
            'accessibility' => 78,
            'best-practices'=> 83,
            'seo'           => 100,
        ],
    );

    expect($stored->performanceScore())->toBe(92)
        ->and($stored->accessibilityScore())->toBe(78)
        ->and($stored->bestPracticesScore())->toBe(83)
        ->and($stored->seoScore())->toBe(100);
});

it('returns null for missing scores', function () {
    $stored = new StoredAuditResults(
        url: 'https://example.com',
        finishedAt: null,
        rawResults: [],
        scores: [],
    );

    expect($stored->performanceScore())->toBeNull()
        ->and($stored->seoScore())->toBeNull();
});
