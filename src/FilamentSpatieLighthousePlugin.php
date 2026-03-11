<?php

namespace FilamentSpatieLighthouse;

use Closure;
use Filament\Panel;
use Filament\Contracts\Plugin;
use Filament\Support\Concerns\EvaluatesClosures;
use FilamentSpatieLighthouse\Pages\LighthouseResults;

class FilamentSpatieLighthousePlugin implements Plugin
{
    use EvaluatesClosures;

    protected bool|Closure $authorizeUsing = true;

    protected bool $navigationGroupSet = false;

    protected string $page = LighthouseResults::class;

    protected string|Closure|null $navigationGroup = null;

    protected int|Closure $navigationSort = 1;

    protected string|Closure $navigationIcon = 'heroicon-o-chart-bar';

    protected string|Closure|null $navigationLabel = null;

    // ──────────────────────────────────────────────────────────────
    //  Lifecycle
    // ──────────────────────────────────────────────────────────────

    public function register(Panel $panel): void
    {
        $panel->pages([$this->getPage()]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    // ──────────────────────────────────────────────────────────────
    //  Factory
    // ──────────────────────────────────────────────────────────────

    public static function make(): static
    {
        return new static;
    }

    public static function get(): static
    {
        /** @var static $instance */
        $instance = filament(app(static::class)->getId());

        return $instance;
    }

    public function getId(): string
    {
        return 'filament-spatie-lighthouse';
    }

    // ──────────────────────────────────────────────────────────────
    //  Authorization
    // ──────────────────────────────────────────────────────────────

    public function authorize(bool|Closure $callback = true): static
    {
        $this->authorizeUsing = $callback;

        return $this;
    }

    public function isAuthorized(): bool
    {
        return $this->evaluate($this->authorizeUsing) === true;
    }

    // ──────────────────────────────────────────────────────────────
    //  Page
    // ──────────────────────────────────────────────────────────────

    public function usingPage(string $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    // ──────────────────────────────────────────────────────────────
    //  Navigation
    // ──────────────────────────────────────────────────────────────

    public function navigationGroup(string|Closure|null $navigationGroup): static
    {
        $this->navigationGroup = $navigationGroup;
        $this->navigationGroupSet = true;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        $navigationGroup = $this->evaluate($this->navigationGroup);

        if ($navigationGroup === null && ! $this->navigationGroupSet) {
            return __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.navigation.group');
        }

        return $navigationGroup;
    }

    public function navigationSort(int|Closure $navigationSort): static
    {
        $this->navigationSort = $navigationSort;

        return $this;
    }

    public function getNavigationSort(): int
    {
        return $this->evaluate($this->navigationSort);
    }

    public function navigationIcon(string|Closure $navigationIcon): static
    {
        $this->navigationIcon = $navigationIcon;

        return $this;
    }

    public function getNavigationIcon(): string
    {
        /** @var string */
        return $this->evaluate($this->navigationIcon);
    }

    public function navigationLabel(string|Closure|null $navigationLabel): static
    {
        $this->navigationLabel = $navigationLabel;

        return $this;
    }

    public function getNavigationLabel(): string
    {
        return $this->evaluate($this->navigationLabel) ?? __('filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.navigation.label');
    }
}
