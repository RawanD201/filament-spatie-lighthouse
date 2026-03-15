@php
    use Illuminate\Support\Str;
    use FilamentSpatieLighthouse\Pages\LighthouseResults;
    use FilamentSpatieLighthouse\Enums\MetricStatus;
@endphp

<x-filament-panels::page>
    @php
        try {
            $lighthouseCssHref = \Filament\Support\Facades\FilamentAsset::getStyleHref('filament-spatie-lighthouse-styles', package: 'filament-spatie-lighthouse');
        } catch (\Throwable) {
            $lighthouseCssHref = null;
        }
    @endphp
    <div x-data="{}" @if($lighthouseCssHref) x-load-css="[@js($lighthouseCssHref)]" @endif>

        {{-- ───────────────────── Results Table ───────────────────── --}}
        <div class="mb-8">
            {{ $this->table }}
        </div>

        @if ($result && isset($categories))

            {{-- ───────────────────── Category Scores ───────────────────── --}}
            @if ($config['show_category_scores'] ?? true)
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    @foreach ([
        'performance' => $categories['performance'],
        'accessibility' => $categories['accessibility'],
        'best-practices' => $categories['best-practices'],
        'seo' => $categories['seo'],
    ] as $categoryKey => $categoryScore)
                        @php
                            $catLabel = $categoryKey === 'best-practices' ? 'best_practices' : $categoryKey;
                        @endphp
                        <x-filament::section :has-content-el="false" :icon="LighthouseResults::scoreIcon($categoryScore)" :icon-color="LighthouseResults::scoreColor($categoryScore)" icon-size="2xl">
                            <x-slot name="heading">
                                {{ __("filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.categories.{$catLabel}") }}
                            </x-slot>
                            <x-slot name="description">
                                {{ $categoryScore !== null ? "{$categoryScore}/100" : __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available') }}
                            </x-slot>
                        </x-filament::section>
                    @endforeach
                </div>
            @endif

            {{-- ───────────────────── Audit Information ───────────────────── --}}
            @if (($config['show_audit_info'] ?? true) && $url)
                <x-filament::section :heading="__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.sections.audit_info')" icon="heroicon-s-information-circle" collapsible class="mb-8">
                    {{-- HTML Report Section --}}
                    @if (($config['show_html_report'] ?? true) && isset($recordId) && $recordId)
                        <x-filament::section :has-content-el="false" icon="heroicon-o-document-text" icon-color="primary"
                            class="mb-6">
                            <x-slot name="heading">
                                {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.view_full_report') }}
                            </x-slot>
                            <x-slot name="description">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.view_full_report_description') }}
                                </p>
                                <div class="flex items-center gap-3">
                                    <x-filament::button tag="a"
                                        href="{{ route('filament-spatie-lighthouse.report.show', $recordId) }}"
                                        target="_blank" icon="heroicon-o-arrow-top-right-on-square" color="primary"
                                        size="sm">
                                        {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.actions.view_html') }}
                                    </x-filament::button>
                                    <x-filament::button tag="a"
                                        href="{{ route('filament-spatie-lighthouse.report.download', $recordId) }}"
                                        icon="heroicon-o-arrow-down-tray" color="gray" size="sm" outlined>
                                        {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.actions.download_html') }}
                                    </x-filament::button>
                                </div>
                            </x-slot>
                        </x-filament::section>
                    @endif

                    {{-- Audit Details --}}
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        {{-- URL --}}
                        <div style="padding: 0.75rem; border-radius: 0.5rem; background: var(--gray-50); dark:background: var(--gray-800);"
                            class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                style="margin-bottom: 0.25rem;">
                                {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.url') }}
                            </div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 break-all">
                                {{ $url }}
                            </div>
                        </div>

                        {{-- Last Ran --}}
                        @if ($lastRanAt)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    style="margin-bottom: 0.25rem;">
                                    {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.last_ran_at', ['time' => '']) }}
                                </div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $lastRanAt->diffForHumans() }}
                                </div>
                            </div>
                        @endif

                        {{-- Form Factor --}}
                        @if (isset($formFactor) && $formFactor)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    style="margin-bottom: 0.25rem;">
                                    {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.form_factor') }}
                                </div>
                                <div>
                                    <x-filament::badge color="info" size="sm">
                                        {{ ucfirst($formFactor) }}
                                    </x-filament::badge>
                                </div>
                            </div>
                        @endif

                        {{-- Lighthouse Version --}}
                        @if (isset($lighthouseVersion) && $lighthouseVersion)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    style="margin-bottom: 0.25rem;">
                                    {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.lighthouse_version') }}
                                </div>
                                <div>
                                    <x-filament::badge color="success" size="sm">
                                        v{{ $lighthouseVersion }}
                                    </x-filament::badge>
                                </div>
                            </div>
                        @endif

                        {{-- User Agent --}}
                        @if (isset($userAgent) && $userAgent)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3" style="grid-column: 1 / -1;">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    style="margin-bottom: 0.25rem;">
                                    {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.user_agent') }}
                                </div>
                                <div class="text-xs text-gray-700 dark:text-gray-300 font-mono break-all">
                                    {{ $userAgent }}
                                </div>
                            </div>
                        @endif

                        {{-- Custom Headers --}}
                        @if (isset($headers) && !empty($headers))
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    style="margin-bottom: 0.25rem;">
                                    {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.headers') }}
                                </div>
                                <div>
                                    <x-filament::badge color="info" size="sm">
                                        {{ count($headers) }}
                                        {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.custom_headers') }}
                                    </x-filament::badge>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-filament::section>
            @endif

            {{-- ───────────────────── Performance Metrics ───────────────────── --}}
            @if (($config['show_performance_metrics'] ?? true) && !empty($metrics))
                <x-filament::section :heading="__(
                    'filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.sections.performance_metrics',
                )" icon="heroicon-s-chart-bar" collapsible class="mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($metrics as $metricKey => $metric)
                            <div x-data="{ showDetails: false }" class="cursor-pointer" @click="showDetails = !showDetails">
                                <x-filament::section :has-content-el="false" :icon="$metric['icon']" :icon-color="$metric['status_color']"
                                    icon-size="lg" class="hover:shadow-lg transition-shadow h-full">
                                    <x-slot name="heading">
                                        <div class="flex items-center justify-between gap-2" style="width: 100%;">
                                            <span class="text-sm">{{ $metric['label'] }}</span>
                                            <div class="flex items-center gap-2">
                                                <x-filament::badge :color="$metric['status_color']" size="sm">
                                                    {{ $metric['status_label'] }}
                                                </x-filament::badge>
                                                {{-- Chevron indicator --}}
                                                <svg style="width: 16px; height: 16px; flex-shrink: 0; transition: transform 0.2s ease;"
                                                    :style="showDetails ?
                                                        'width: 16px; height: 16px; flex-shrink: 0; transition: transform 0.2s ease; transform: rotate(180deg)' :
                                                        'width: 16px; height: 16px; flex-shrink: 0; transition: transform 0.2s ease; transform: rotate(0deg)'"
                                                    class="text-gray-400 dark:text-gray-500" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </div>
                                        </div>
                                    </x-slot>
                                    <x-slot name="description">
                                        <div class="text-2xl font-bold mt-2">{{ $metric['formatted'] }}</div>

                                        @if (isset($metric['raw_display']))
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $metric['raw_display'] }}
                                            </div>
                                        @endif

                                        {{-- Expandable Details --}}
                                        <div x-show="showDetails" x-cloak
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-1"
                                            class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                                {{ $metric['description'] }}
                                            </p>

                                            @if (isset($metric['thresholds']))
                                                @php
                                                    $isScore = ($metric['thresholds']['unit'] ?? 'ms') === 'score';
                                                    $decimals = $isScore ? 3 : 0;
                                                    $suffix = $isScore ? '' : ' ms';
                                                @endphp
                                                <div class="space-y-2 text-xs">
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-500 dark:text-gray-400">
                                                            {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.thresholds.good') }}:
                                                        </span>
                                                        <x-filament::badge color="success" size="sm">
                                                            ≤
                                                            {{ number_format($metric['thresholds']['good'], $decimals) }}{{ $suffix }}
                                                        </x-filament::badge>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-500 dark:text-gray-400">
                                                            {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.thresholds.needs_improvement') }}:
                                                        </span>
                                                        <x-filament::badge color="warning" size="sm">
                                                            ≤
                                                            {{ number_format($metric['thresholds']['needs_improvement'], $decimals) }}{{ $suffix }}
                                                        </x-filament::badge>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-500 dark:text-gray-400">
                                                            {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.thresholds.poor') }}:
                                                        </span>
                                                        <x-filament::badge color="danger" size="sm">
                                                            &gt;
                                                            {{ number_format($metric['thresholds']['needs_improvement'], $decimals) }}{{ $suffix }}
                                                        </x-filament::badge>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </x-slot>
                                </x-filament::section>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            {{-- ───────────────────── Failed Audits ───────────────────── --}}
            @if (($config['show_failed_audits'] ?? true) && !empty($failedAudits))
                @php
                    $initialCount = (int) ($config['failed_audits_initial_count'] ?? 10);
                    $maxHeight = $config['failed_audits_max_height'] ?? '800px';
                @endphp

                <x-filament::section icon="heroicon-s-exclamation-triangle" icon-color="warning" collapsible
                    class="mb-8">
                    <x-slot name="heading">
                        {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.sections.failed_audits') }}
                        <x-filament::badge color="warning" size="sm"
                            style="display: inline-flex; margin-left: 0.5rem;">
                            {{ count($failedAudits) }}
                        </x-filament::badge>
                    </x-slot>
                    <div x-data="{ showAll: false }" class="space-y-4">
                        <div class="space-y-4"
                            :class="showAll &&
                                '{{ count($failedAudits) > $initialCount ? 'max-h-[' . $maxHeight . '] overflow-y-auto pr-2' : '' }}'"
                            style="scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.5) transparent;">
                            @foreach ($failedAudits as $auditName => $audit)
                                <div x-show="showAll || {{ $loop->index }} < {{ $initialCount }}"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100">
                                    <x-filament::section :has-content-el="false" icon="heroicon-o-exclamation-triangle"
                                        icon-color="warning" icon-size="sm">
                                        <x-slot name="heading">
                                            {{ $audit['title'] ?? $auditName }}
                                        </x-slot>
                                        <x-slot name="description">
                                            <div class="space-y-3">
                                                @if (isset($audit['description']))
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {!! $audit['description'] !!}
                                                    </p>
                                                @endif

                                                <div class="flex flex-wrap items-center gap-2">
                                                    @if (isset($audit['displayValue']))
                                                        <x-filament::badge color="warning" size="sm">
                                                            {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.current_value') }}:
                                                            {{ $audit['displayValue'] }}
                                                        </x-filament::badge>
                                                    @endif

                                                    @if (isset($audit['score']))
                                                        <x-filament::badge :color="$audit['score'] * 100 >= 50 ? 'warning' : 'danger'" size="lg">
                                                            {{ number_format($audit['score'] * 100, 0) }}/100
                                                        </x-filament::badge>
                                                    @endif
                                                </div>
                                            </div>
                                        </x-slot>
                                    </x-filament::section>
                                </div>
                            @endforeach
                        </div>

                        @if (count($failedAudits) > $initialCount)
                            <div class="flex justify-center pt-2">
                                <x-filament::button @click="showAll = !showAll" color="gray" size="sm"
                                    outlined>
                                    <span x-show="!showAll" class="flex items-center gap-2">
                                        {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.show_all_audits', ['count' => count($failedAudits)]) }}
                                        <x-filament::icon icon="heroicon-o-chevron-down" class="w-4 h-4" />
                                    </span>
                                    <span x-show="showAll" class="flex items-center gap-2">
                                        {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.show_less') }}
                                        <x-filament::icon icon="heroicon-o-chevron-up" class="w-4 h-4" />
                                    </span>
                                </x-filament::button>
                            </div>
                        @endif
                    </div>
                </x-filament::section>
            @endif

            {{-- ───────────────────── Audit History ───────────────────── --}}
            @if (($config['show_history'] ?? true) && !empty($history) && count($history) > 1)
                <x-filament::section :heading="__('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.sections.history')" icon="heroicon-s-clock" collapsible collapsed class="mb-8">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.messages.history_description') }}
                    </p>

                    <div class="space-y-3">
                        @php
                            $historyCount = (int) ($config['history_count'] ?? 5);
                        @endphp
                        @foreach (array_slice($history, 1, $historyCount) as $item)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="flex flex-wrap items-center gap-3">
                                    @foreach ([
        'performance' => $item['performance_score'] ?? null,
        'accessibility' => $item['accessibility_score'] ?? null,
        'best_practices' => $item['best_practices_score'] ?? null,
        'seo' => $item['seo_score'] ?? null,
    ] as $label => $score)
                                        <div class="text-sm">
                                            <span class="font-semibold">
                                                {{ __("filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.table.{$label}") }}:
                                            </span>
                                            <x-filament::badge :color="LighthouseResults::scoreColor($score)" size="sm">
                                                {{ $score !== null ? "{$score}/100" : __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.not_available') }}
                                            </x-filament::badge>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400 shrink-0 ml-3">
                                    @if (isset($item['finished_at']))
                                        {{ \Carbon\Carbon::parse($item['finished_at'])->diffForHumans() }}
                                    @elseif (isset($item['created_at']))
                                        {{ \Carbon\Carbon::parse($item['created_at'])->diffForHumans() }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

        @endif
    </div>
</x-filament-panels::page>
