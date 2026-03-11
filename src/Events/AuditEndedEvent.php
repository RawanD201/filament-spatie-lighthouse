<?php

namespace FilamentSpatieLighthouse\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuditEndedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array<string, int|null>  $scores  Keyed by category (performance, accessibility, best-practices, seo). Values are 0–100.
     */
    public function __construct(
        public string $url,
        public array $scores,
        public ?string $userId = null,
    ) {
    }
}
