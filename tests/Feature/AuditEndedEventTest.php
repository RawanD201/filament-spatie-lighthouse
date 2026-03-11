<?php

use FilamentSpatieLighthouse\Events\AuditEndedEvent;

it('stores scores as a plain array', function () {
    $scores = [
        'performance'    => 88,
        'accessibility'  => 95,
        'best-practices' => 75,
        'seo'            => 91,
    ];

    $event = new AuditEndedEvent(url: 'https://example.com', scores: $scores);

    expect($event->url)->toBe('https://example.com')
        ->and($event->scores)->toBe($scores)
        ->and($event->scores['performance'])->toBe(88);
});

it('is serializable', function () {
    $event = new AuditEndedEvent(
        url: 'https://example.com',
        scores: ['performance' => 90],
    );

    $serialized = serialize($event);

    expect(unserialize($serialized))->toBeInstanceOf(AuditEndedEvent::class);
});
