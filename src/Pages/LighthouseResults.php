<?php

namespace FilamentSpatieLighthouse\Pages;

use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Spatie\Lighthouse\Lighthouse;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Spatie\Lighthouse\Enums\Category;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Spatie\Lighthouse\Enums\FormFactor;
use Spatie\Lighthouse\LighthouseResult;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\CheckboxList;
use FilamentSpatieLighthouse\Enums\MetricStatus;
use Filament\Tables\Concerns\InteractsWithTable;
use FilamentSpatieLighthouse\Events\AuditEndedEvent;
use FilamentSpatieLighthouse\Events\AuditFailedEvent;
use FilamentSpatieLighthouse\ResultStores\ResultStore;
use FilamentSpatieLighthouse\Events\AuditStartingEvent;
use FilamentSpatieLighthouse\Jobs\RunLighthouseAuditJob;
use FilamentSpatieLighthouse\Models\LighthouseAuditResult;
use FilamentSpatieLighthouse\FilamentSpatieLighthousePlugin;

class LighthouseResults extends Page implements HasTable
{
    use InteractsWithTable;

    /** @var array<string, string> */
    protected $listeners = ['refresh-component' => '$refresh'];

    protected string $view = 'filament-spatie-lighthouse::pages.lighthouse-results';

    /** URL used for the detail panel below the table. */
    public ?string $url = null;

    /** @deprecated No longer used for table filtering. Kept for backward compat. */
    public ?string $selectedUrl = null;

    // ──────────────────────────────────────────────────────────────
    //  Navigation
    // ──────────────────────────────────────────────────────────────

    public function getHeading(): string|Htmlable
    {
        return __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.heading');
    }

    public function getTitle(): string|Htmlable
    {
        return FilamentSpatieLighthousePlugin::get()->getNavigationLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentSpatieLighthousePlugin::get()->getNavigationGroup();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentSpatieLighthousePlugin::get()->getNavigationLabel();
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentSpatieLighthousePlugin::get()->getNavigationSort();
    }

    public static function getNavigationIcon(): string
    {
        return FilamentSpatieLighthousePlugin::get()->getNavigationIcon();
    }

    public static function canAccess(): bool
    {
        return FilamentSpatieLighthousePlugin::get()->isAuthorized();
    }

    // ──────────────────────────────────────────────────────────────
    //  Header Actions
    // ──────────────────────────────────────────────────────────────

    protected function getActions(): array
    {
        return [
            $this->makeRunAuditAction(),
            Action::make(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.buttons.refresh'))
                ->button()
                ->icon('heroicon-o-arrow-path')
                ->action('refresh'),
        ];
    }

    protected function makeRunAuditAction(): Action
    {
        return Action::make(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.buttons.run_audit'))
            ->button()
            ->icon('heroicon-o-play')
            ->schema([
                TextInput::make('url')
                    ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.url'))
                    ->url()
                    ->required()
                    ->placeholder(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.url_placeholder'))
                    ->default(fn() => $this->url)
                    ->helperText(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.url_helper')),

                CheckboxList::make('categories')
                    ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.categories'))
                    ->options([
                        'performance' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.categories.performance'),
                        'accessibility' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.categories.accessibility'),
                        'best-practices' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.categories.best_practices'),
                        'seo' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.categories.seo'),
                    ])
                    ->default(config('filament-spatie-lighthouse.default_categories', ['performance', 'accessibility', 'best-practices', 'seo']))
                    ->columns(2)
                    ->helperText(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.categories_helper')),

                Select::make('form_factor')
                    ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.form_factor'))
                    ->options([
                        'desktop' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.desktop'),
                        'mobile' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.mobile'),
                    ])
                    ->default('desktop')
                    ->helperText(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.form_factor_helper')),

                TextInput::make('user_agent')
                    ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.user_agent'))
                    ->placeholder(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.user_agent_placeholder'))
                    ->helperText(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.user_agent_helper')),

                KeyValue::make('headers')
                    ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.headers'))
                    ->keyLabel(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.header_name'))
                    ->valueLabel(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.header_value'))
                    ->helperText(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.headers_helper')),

                Toggle::make('throttle_cpu')
                    ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.throttle_cpu'))
                    ->default(false)
                    ->helperText(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.throttle_cpu_helper')),

                Toggle::make('throttle_network')
                    ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.throttle_network'))
                    ->default(false)
                    ->helperText(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.throttle_network_helper')),

                TextInput::make('timeout')
                    ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.timeout'))
                    ->numeric()
                    ->default(config('filament-spatie-lighthouse.default_timeout', 180))
                    ->suffix(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.timeout_suffix'))
                    ->helperText(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form.timeout_helper')),
            ])
            ->action(function (array $data) {
                $this->url = $data['url'];
                $this->runAudit(
                    $data['url'],
                    $data['categories'] ?? [],
                    $data['form_factor'] ?? 'desktop',
                    $data['user_agent'] ?? null,
                    $data['headers'] ?? [],
                    $data['throttle_cpu'] ?? false,
                    $data['throttle_network'] ?? false,
                    (int) ($data['timeout'] ?? 180),
                );
            });
    }

    // ──────────────────────────────────────────────────────────────
    //  Table
    // ──────────────────────────────────────────────────────────────

    public function table(Table $table): Table
    {
        $pollInterval = config('filament-spatie-lighthouse.display.table_poll_interval', '30s');

        $table = $table
            ->query(LighthouseAuditResult::query()->latest())
            ->columns($this->getTableColumns())
            ->recordActions($this->getTableRecordActions())
            ->filters($this->getTableFilters())
            ->toolbarActions($this->getTableToolbarActions())
            ->emptyStateHeading(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.sections.no_results'))
            ->emptyStateDescription(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.messages.no_results'))
            ->emptyStateIcon('heroicon-o-information-circle')
            ->defaultSort('finished_at', 'desc');

        if ($pollInterval) {
            $table->poll($pollInterval);
        }

        return $table;
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('url')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.url'))
                ->searchable()
                ->sortable(),
            TextColumn::make('performance_score')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.performance'))
                ->formatStateUsing(fn($state) => $state !== null ? $state . '/100' : __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'))
                ->color(fn($state) => MetricStatus::fromScore($state)->color())
                ->sortable(),
            TextColumn::make('accessibility_score')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.accessibility'))
                ->formatStateUsing(fn($state) => $state !== null ? $state . '/100' : __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'))
                ->color(fn($state) => MetricStatus::fromScore($state)->color())
                ->sortable(),
            TextColumn::make('best_practices_score')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.best_practices'))
                ->formatStateUsing(fn($state) => $state !== null ? $state . '/100' : __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'))
                ->color(fn($state) => MetricStatus::fromScore($state)->color())
                ->sortable(),
            TextColumn::make('seo_score')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.seo'))
                ->formatStateUsing(fn($state) => $state !== null ? $state . '/100' : __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'))
                ->color(fn($state) => MetricStatus::fromScore($state)->color())
                ->sortable(),
            TextColumn::make('finished_at')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.finished_at'))
                ->dateTime()
                ->sortable(),
        ];
    }

    protected function getTableRecordActions(): array
    {
        $actionConfig = config('filament-spatie-lighthouse.display.table_actions', []);
        $actions = [];

        if ($actionConfig['view'] ?? true) {
            $actions[] = Action::make('view')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.actions.view'))
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->button()
                ->size('sm')
                ->action(function ($record) {
                    // Only set the detail URL — do NOT filter the table.
                    $this->url = $record->url;
                    $this->dispatch('refresh-component');
                });
        }

        if ($actionConfig['view_html'] ?? true) {
            $actions[] = Action::make('view_html')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.actions.view_html'))
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->button()
                ->size('sm')
                ->url(fn($record) => route('filament-spatie-lighthouse.report.show', $record->id))
                ->openUrlInNewTab();
        }

        if ($actionConfig['download_html'] ?? true) {
            $actions[] = Action::make('download_html')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.actions.download_html'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->button()
                ->size('sm')
                ->url(fn($record) => route('filament-spatie-lighthouse.report.download', $record->id));
        }

        if ($actionConfig['delete'] ?? true) {
            $actions[] = Action::make('delete')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.actions.delete'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->button()
                ->size('sm')
                ->requiresConfirmation()
                ->modalHeading(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.confirm.delete_title'))
                ->modalDescription(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.confirm.delete_description'))
                ->action(function ($record) {
                    $record->delete();
                    $this->dispatch('refresh-component');
                    Notification::make()
                        ->title(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.notifications.deleted'))
                        ->success()
                        ->send();
                });
        }

        return $actions;
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('performance_score')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.performance'))
                ->options([
                    'excellent' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.filters.excellent'),
                    'good' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.filters.good'),
                    'poor' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.filters.poor'),
                ])
                ->query(fn(Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                    'excellent' => $query->where('performance_score', '>=', 90),
                    'good' => $query->whereBetween('performance_score', [50, 89]),
                    'poor' => $query->where('performance_score', '<', 50),
                    default => $query,
                }),
            SelectFilter::make('accessibility_score')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.accessibility'))
                ->options([
                    'excellent' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.filters.excellent'),
                    'good' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.filters.good'),
                    'poor' => __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.filters.poor'),
                ])
                ->query(fn(Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                    'excellent' => $query->where('accessibility_score', '>=', 90),
                    'good' => $query->whereBetween('accessibility_score', [50, 89]),
                    'poor' => $query->where('accessibility_score', '<', 50),
                    default => $query,
                }),
            Filter::make('finished_at')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.finished_at'))
                ->schema([
                    DatePicker::make('created_from')
                        ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.filters.created_from')),
                    DatePicker::make('created_until')
                        ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.filters.created_until')),
                ])
                ->query(
                    fn(Builder $query, array $data): Builder => $query
                        ->when($data['created_from'], fn(Builder $q, $date) => $q->whereDate('finished_at', '>=', $date))
                        ->when($data['created_until'], fn(Builder $q, $date) => $q->whereDate('finished_at', '<=', $date))
                ),
        ];
    }

    protected function getTableToolbarActions(): array
    {
        $actions = [
            BulkAction::make('delete')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.actions.delete'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.confirm.bulk_delete_title'))
                ->modalDescription(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.confirm.bulk_delete_description'))
                ->action(function ($records) {
                    $count = $records->count();
                    $records->each->delete();
                    $this->dispatch('refresh-component');
                    Notification::make()
                        ->title(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.notifications.bulk_deleted', ['count' => $count]))
                        ->success()
                        ->send();
                }),
        ];

        if (config('filament-spatie-lighthouse.export.enabled', true)) {
            $actions[] = BulkAction::make('export_csv')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.actions.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn($records) => $this->exportRecords($records, 'csv'));

            $actions[] = BulkAction::make('export_json')
                ->label(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.actions.export_json'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn($records) => $this->exportRecords($records, 'json'));
        }

        return $actions;
    }

    // ──────────────────────────────────────────────────────────────
    //  View Data
    // ──────────────────────────────────────────────────────────────

    protected function getViewData(): array
    {
        $displayConfig = config('filament-spatie-lighthouse.display', []);
        $currentUrl = $this->url;

        $baseData = [
            'result' => null,
            'url' => $currentUrl,
            'lastRanAt' => null,
            'recordId' => null,
            'categories' => $this->buildEmptyCategories(),
            'metrics' => [],
            'failedAudits' => [],
            'passedAudits' => [],
            'formFactor' => null,
            'userAgent' => null,
            'headers' => [],
            'lighthouseVersion' => null,
            'history' => [],
            'config' => $displayConfig,
        ];

        // Safely fetch stored results — catches type errors from nullable DB columns.
        $resultStore = app(ResultStore::class);
        $storedResults = rescue(fn() => $resultStore->latestResults($currentUrl), null, false);

        if (! $storedResults || empty($storedResults->rawResults)) {
            return $baseData;
        }

        try {
            $result = new LighthouseResult($storedResults->rawResults);
        } catch (\Exception $e) {
            return $baseData;
        }

        $latestRecord = rescue(
            fn() => LighthouseAuditResult::where('url', $storedResults->url)->latest()->first(),
            null,
            false,
        );

        [$failedAudits, $passedAudits] = $this->partitionAudits($result);
        $historyCount = (int) ($displayConfig['history_count'] ?? 5);

        // Safely extract metadata — each call wrapped individually
        // so one failure doesn't hide all data.
        $formFactor = rescue(fn() => $result->formFactor()->value, 'desktop', false);
        $userAgent = rescue(fn() => $result->userAgent(), null, false);
        $headers = rescue(fn() => $result->headers(), [], false);
        $lighthouseVersion = rescue(fn() => $result->lighthouseVersion(), null, false);
        $history = rescue(
            fn() => $resultStore->getHistory($currentUrl, $historyCount + 1),
            [],
            false,
        );

        return array_merge($baseData, [
            'result' => $result,
            'url' => $storedResults->url,
            'lastRanAt' => $storedResults->finishedAt,
            'recordId' => $latestRecord?->id,
            'categories' => $this->buildCategories($storedResults->scores ?? []),
            'metrics' => $this->buildMetrics($result),
            'failedAudits' => $failedAudits,
            'passedAudits' => $passedAudits,
            'formFactor' => $formFactor,
            'userAgent' => $userAgent,
            'headers' => $headers ?? [],
            'lighthouseVersion' => $lighthouseVersion,
            'history' => $history ?? [],
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  Data Builders
    // ──────────────────────────────────────────────────────────────

    protected function buildEmptyCategories(): array
    {
        return [
            'performance' => null,
            'accessibility' => null,
            'best-practices' => null,
            'seo' => null,
        ];
    }

    protected function buildCategories(array $scores): array
    {
        return [
            'performance' => $scores['performance'] ?? null,
            'accessibility' => $scores['accessibility'] ?? null,
            'best-practices' => $scores['best-practices'] ?? null,
            'seo' => $scores['seo'] ?? null,
        ];
    }

    protected function buildMetrics(LighthouseResult $result): array
    {
        $configThresholds = config('filament-spatie-lighthouse.metric_thresholds', []);
        $metrics = [];

        // Each metric is extracted individually so one failure
        // doesn't prevent other metrics from showing.
        $metricExtractors = [
            'first_contentful_paint' => fn() => [
                'formatted' => $result->formattedFirstContentfulPaint(),
                'ms' => $result->firstContentfulPaintInMs(),
            ],
            'largest_contentful_paint' => fn() => [
                'formatted' => $result->formattedLargestContentfulPaint(),
                'ms' => $result->largestContentfulPaintInMs(),
            ],
            'speed_index' => fn() => [
                'formatted' => $result->formattedSpeedIndex(),
                'ms' => $result->speedIndexInMs(),
            ],
            'total_blocking_time' => fn() => [
                'formatted' => $result->formattedTotalBlockingTime(),
                'ms' => $result->totalBlockingTimeInMs(),
            ],
            'time_to_interactive' => fn() => [
                'formatted' => $result->formattedTimeToInteractive(),
                'ms' => $result->timeToInteractiveInMs(),
            ],
            'cumulative_layout_shift' => fn() => [
                'formatted' => $result->formattedCumulativeLayoutShift(),
                'value' => $result->cumulativeLayoutShift(),
            ],
            'total_page_size' => fn() => [
                'formatted' => self::formatBytes($result->totalPageSizeInBytes()),
                'bytes' => $result->totalPageSizeInBytes(),
            ],
        ];

        foreach ($metricExtractors as $key => $extractor) {
            try {
                $data = $extractor();
            } catch (\Exception $e) {
                continue; // Skip this metric, show the rest
            }

            $thresholds = $configThresholds[$key] ?? null;

            $metric = [
                'key' => $key,
                'label' => __("filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.metrics.{$key}"),
                'description' => __("filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.metric_descriptions.{$key}"),
                'icon' => __("filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.metric_icons.{$key}"),
                'formatted' => $data['formatted'] ?? __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'),
            ];

            if (isset($data['ms'])) {
                $metric['raw_value'] = $data['ms'];
                $metric['raw_unit'] = 'ms';
                $metric['raw_display'] = number_format($data['ms'], 0) . ' ' . __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.units.ms');
            } elseif (isset($data['value'])) {
                $metric['raw_value'] = $data['value'];
                $metric['raw_unit'] = 'score';
                $metric['raw_display'] = number_format($data['value'], 3);
            } elseif (isset($data['bytes'])) {
                $metric['raw_value'] = $data['bytes'];
                $metric['raw_unit'] = 'bytes';
                $metric['raw_display'] = number_format($data['bytes'], 0) . ' ' . __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.units.bytes');
            }

            if ($thresholds) {
                $metric['thresholds'] = $thresholds;
                $isFloat = ($thresholds['unit'] ?? 'ms') === 'score';
                $status = MetricStatus::fromValue(
                    $metric['raw_value'] ?? 0,
                    $thresholds['good'],
                    $thresholds['needs_improvement'],
                );
                $metric['status'] = $status;
                $metric['status_color'] = $status->color();
                $metric['status_label'] = $status->label();
            } else {
                $metric['status'] = MetricStatus::Unknown;
                $metric['status_color'] = MetricStatus::Unknown->color();
                $metric['status_label'] = MetricStatus::Unknown->label();
            }

            $metrics[$key] = $metric;
        }

        return $metrics;
    }

    /**
     * @return array{0: array, 1: array}
     */
    protected function partitionAudits(LighthouseResult $result): array
    {
        $failed = [];
        $passed = [];

        try {
            foreach ($result->audits() as $name => $audit) {
                if (isset($audit['score']) && $audit['score'] !== null) {
                    if ($audit['score'] < 0.9) {
                        $failed[$name] = $audit;
                    } else {
                        $passed[$name] = $audit;
                    }
                }
            }
        } catch (\Exception $e) {
            // Audits unavailable
        }

        return [$failed, $passed];
    }

    // ──────────────────────────────────────────────────────────────
    //  Audit Runner
    // ──────────────────────────────────────────────────────────────

    public function runAudit(
        string $url,
        array $categories = [],
        string $formFactor = 'desktop',
        ?string $userAgent = null,
        array $headers = [],
        bool $throttleCpu = false,
        bool $throttleNetwork = false,
        int $timeout = 180,
    ): void {
        try {
            if (config('filament-spatie-lighthouse.use_queue', false)) {
                $this->dispatchQueuedAudit($url, $categories, $formFactor, $userAgent, $headers, $throttleCpu, $throttleNetwork, $timeout);
            } else {
                $this->runSynchronousAudit($url, $categories, $formFactor, $userAgent, $headers, $throttleCpu, $throttleNetwork, $timeout);
            }
        } catch (\Exception $e) {
            event(new AuditFailedEvent(url: $url, exception: $e, userId: Auth::id()));

            Notification::make()
                ->title(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.notifications.audit_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function dispatchQueuedAudit(
        string $url,
        array $categories,
        string $formFactor,
        ?string $userAgent,
        array $headers,
        bool $throttleCpu,
        bool $throttleNetwork,
        int $timeout,
    ): void {
        $job = new RunLighthouseAuditJob(
            url: $url,
            categories: $categories,
            formFactor: $formFactor,
            userAgent: $userAgent,
            headers: $headers,
            throttleCpu: $throttleCpu,
            throttleNetwork: $throttleNetwork,
            timeoutSeconds: $timeout,
            userId: Auth::id(),
        );

        $connection = config('filament-spatie-lighthouse.queue_connection');
        $queue = config('filament-spatie-lighthouse.queue_name', 'default');

        if ($connection) {
            $job->onConnection($connection);
        }

        if ($queue !== 'default') {
            $job->onQueue($queue);
        }

        dispatch($job);

        Notification::make()
            ->title(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.notifications.audit_queued'))
            ->body(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.notifications.audit_queued_description'))
            ->info()
            ->send();

        $this->dispatch('refresh-component');
    }

    protected function runSynchronousAudit(
        string $url,
        array $categories,
        string $formFactor,
        ?string $userAgent,
        array $headers,
        bool $throttleCpu,
        bool $throttleNetwork,
        int $timeout,
    ): void {
        event(new AuditStartingEvent(url: $url, categories: $categories, formFactor: $formFactor, userId: Auth::id()));

        Notification::make()
            ->title(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.notifications.audit_running'))
            ->info()
            ->send();

        $lighthouse = Lighthouse::url($url)->timeoutInSeconds($timeout);

        if (! empty($categories)) {
            $lighthouse->categories(...array_map(fn($cat) => Category::fromString($cat), $categories));
        }

        $lighthouse->formFactor($formFactor === 'mobile' ? FormFactor::Mobile : FormFactor::Desktop);

        if ($userAgent) {
            $lighthouse->userAgent($userAgent);
        }

        if (! empty($headers)) {
            $lighthouse->headers($headers);
        }

        if ($throttleCpu) {
            $lighthouse->throttleCpu();
        }

        if ($throttleNetwork) {
            $lighthouse->throttleNetwork();
        }

        $result = $lighthouse->run();
        app(ResultStore::class)->save($url, $result);

        event(new AuditEndedEvent(url: $url, scores: $result->scores(), userId: Auth::id()));

        $this->url = $url;
        $this->dispatch('refresh-component');

        Notification::make()
            ->title(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.notifications.audit_completed'))
            ->success()
            ->send();
    }

    // ──────────────────────────────────────────────────────────────
    //  Export
    // ──────────────────────────────────────────────────────────────

    public function exportRecords($records, string $format = 'csv'): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $format = in_array($format, ['csv', 'json']) ? $format : 'csv';
        $prefix = __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.export.filename_prefix');
        $filename = $prefix . '-' . now()->format('Y-m-d-His') . '.' . $format;

        return response()->streamDownload(function () use ($records, $format) {
            $handle = fopen('php://output', 'w');

            if ($format === 'csv') {
                fputcsv($handle, [
                    __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.export.columns.url'),
                    __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.export.columns.performance_score'),
                    __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.export.columns.accessibility_score'),
                    __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.export.columns.best_practices_score'),
                    __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.export.columns.seo_score'),
                    __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.export.columns.finished_at'),
                ]);
                foreach ($records as $record) {
                    fputcsv($handle, [
                        $record->url,
                        $record->performance_score ?? __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'),
                        $record->accessibility_score ?? __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'),
                        $record->best_practices_score ?? __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'),
                        $record->seo_score ?? __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'),
                        $record->finished_at?->toDateTimeString() ?? __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available'),
                    ]);
                }
            } else {
                $data = $records->map(fn($record) => [
                    'url' => $record->url,
                    'performance_score' => $record->performance_score,
                    'accessibility_score' => $record->accessibility_score,
                    'best_practices_score' => $record->best_practices_score,
                    'seo_score' => $record->seo_score,
                    'finished_at' => $record->finished_at?->toIso8601String(),
                ])->toArray();

                fwrite($handle, json_encode($data, JSON_PRETTY_PRINT));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/json',
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  Helpers (static for use in Blade)
    // ──────────────────────────────────────────────────────────────

    public static function scoreColor(int|float|null $score): string
    {
        return MetricStatus::fromScore($score)->color();
    }

    public static function scoreIcon(int|float|null $score): string
    {
        return MetricStatus::fromScore($score)->icon();
    }

    public static function statusColor(string $status): string
    {
        $enum = MetricStatus::tryFrom($status) ?? MetricStatus::Unknown;

        return $enum->color();
    }

    public static function formatBytes(?int $bytes): string
    {
        if ($bytes === null) {
            return __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available');
        }

        $unitKeys = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($unitKeys) - 1);
        $bytes /= (1 << (10 * $pow));

        $unitLabel = __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.units.' . $unitKeys[$pow]);

        return round($bytes, 2) . ' ' . $unitLabel;
    }

    public function refresh(): void
    {
        $this->dispatch('refresh-component');

        Notification::make()
            ->title(__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.notifications.results_refreshed'))
            ->success()
            ->send();
    }
}
