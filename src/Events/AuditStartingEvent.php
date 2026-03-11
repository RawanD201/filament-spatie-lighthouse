<?php

namespace FilamentSpatieLighthouse\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuditStartingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $url,
        public array $categories = [],
        public string $formFactor = 'desktop',
        public ?string $userId = null,
    ) {
    }
}
