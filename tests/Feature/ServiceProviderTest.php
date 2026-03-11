<?php

use FilamentSpatieLighthouse\ResultStores\DatabaseResultStore;
use FilamentSpatieLighthouse\ResultStores\ResultStore;

it('binds ResultStore as a singleton', function () {
    expect(app(ResultStore::class))->toBeInstanceOf(DatabaseResultStore::class);
    expect(app(ResultStore::class))->toBe(app(ResultStore::class));
});

it('uses cache store when configured', function () {
    config()->set('filament-spatie-lighthouse.result_store', 'cache');

    // Re-bind to simulate fresh resolution
    app()->forgetInstance(ResultStore::class);
    app()->singleton(ResultStore::class, fn () => new \FilamentSpatieLighthouse\ResultStores\CacheResultStore());

    expect(app(ResultStore::class))->toBeInstanceOf(\FilamentSpatieLighthouse\ResultStores\CacheResultStore::class);
});
