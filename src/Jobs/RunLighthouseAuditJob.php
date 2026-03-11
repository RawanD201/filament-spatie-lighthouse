<?php

namespace FilamentSpatieLighthouse\Jobs;

use FilamentSpatieLighthouse\Events\AuditEndedEvent;
use FilamentSpatieLighthouse\Events\AuditFailedEvent;
use FilamentSpatieLighthouse\Events\AuditStartingEvent;
use FilamentSpatieLighthouse\Models\LighthouseAuditResult;
use FilamentSpatieLighthouse\ResultStores\ResultStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\Lighthouse\Enums\Category;
use Spatie\Lighthouse\Enums\FormFactor;
use Spatie\Lighthouse\Lighthouse;
use Spatie\Lighthouse\LighthouseResult;

class RunLighthouseAuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout;
    public int $tries;

    public function __construct(
        public string $url,
        public array $categories = [],
        public string $formFactor = 'desktop',
        public ?string $userAgent = null,
        public array $headers = [],
        public bool $throttleCpu = false,
        public bool $throttleNetwork = false,
        public int $timeoutSeconds = 180,
        public ?string $userId = null,
    ) {
        $this->timeout = $timeoutSeconds;
        $this->tries = config('filament-spatie-lighthouse.queue_tries', 1);
    }

    public function handle(): void
    {
        try {
            // Dispatch starting event
            event(new AuditStartingEvent(
                url: $this->url,
                categories: $this->categories,
                formFactor: $this->formFactor,
                userId: $this->userId,
            ));

            $lighthouse = Lighthouse::url($this->url)
                ->timeoutInSeconds($this->timeoutSeconds);

            // Configure categories
            if (!empty($this->categories)) {
                $categoryEnums = array_map(fn($cat) => Category::fromString($cat), $this->categories);
                $lighthouse->categories(...$categoryEnums);
            }

            // Configure form factor
            if ($this->formFactor === 'mobile') {
                $lighthouse->formFactor(FormFactor::Mobile);
            } else {
                $lighthouse->formFactor(FormFactor::Desktop);
            }

            // Configure user agent
            if ($this->userAgent) {
                $lighthouse->userAgent($this->userAgent);
            }

            // Configure headers
            if (!empty($this->headers)) {
                $lighthouse->headers($this->headers);
            }

            // Configure throttling
            if ($this->throttleCpu) {
                $lighthouse->throttleCpu();
            }

            if ($this->throttleNetwork) {
                $lighthouse->throttleNetwork();
            }

            $result = $lighthouse->run();

            $resultStore = app(ResultStore::class);
            $resultStore->save($this->url, $result);

            // Dispatch ended event
            event(new AuditEndedEvent(
                url: $this->url,
                scores: $result->scores(),
                userId: $this->userId,
            ));

            Log::info("Lighthouse audit completed for URL: {$this->url}");
        } catch (\Exception $e) {
            Log::error("Lighthouse audit failed for URL: {$this->url}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Dispatch failure event
            event(new AuditFailedEvent(
                url: $this->url,
                exception: $e,
                userId: $this->userId,
            ));

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Lighthouse audit job failed permanently for URL: {$this->url}", [
            'error' => $exception->getMessage(),
        ]);

        // Dispatch failure event
        event(new AuditFailedEvent(
            url: $this->url,
            exception: $exception,
            userId: $this->userId,
        ));
    }
}
