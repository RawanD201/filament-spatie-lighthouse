<?php

namespace FilamentSpatieLighthouse\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class AuditFailedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $url,
        public Throwable $exception,
        public ?string $userId = null,
    ) {
    }
}
