<?php

namespace FilamentSpatieLighthouse\Enums;

enum MetricStatus: string
{
    case Good = 'good';
    case NeedsImprovement = 'needs_improvement';
    case Poor = 'poor';
    case Unknown = 'unknown';

    /**
     * Determine status from a metric value and its thresholds.
     */
    public static function fromValue(float|int $value, float|int $good, float|int $needsImprovement): self
    {
        if ($value <= $good) {
            return self::Good;
        }

        if ($value <= $needsImprovement) {
            return self::NeedsImprovement;
        }

        return self::Poor;
    }

    /**
     * Determine status from a category score (0–100).
     */
    public static function fromScore(int|float|null $score): self
    {
        if ($score === null) {
            return self::Unknown;
        }

        $thresholds = config('filament-spatie-lighthouse.score_thresholds', [
            'good' => 90,
            'needs_improvement' => 50,
        ]);

        if ($score >= $thresholds['good']) {
            return self::Good;
        }

        if ($score >= $thresholds['needs_improvement']) {
            return self::NeedsImprovement;
        }

        return self::Poor;
    }

    /**
     * Filament color string for badges/icons.
     */
    public function color(): string
    {
        return match ($this) {
            self::Good => 'success',
            self::NeedsImprovement => 'warning',
            self::Poor => 'danger',
            self::Unknown => 'gray',
        };
    }

    /**
     * Heroicon name representing this status.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Good => 'heroicon-s-check-circle',
            self::NeedsImprovement => 'heroicon-s-exclamation-circle',
            self::Poor => 'heroicon-s-x-circle',
            self::Unknown => 'heroicon-s-question-mark-circle',
        };
    }

    /**
     * Translated label.
     */
    public function label(): string
    {
        return __("filament-spatie-lighthouse::lighthouse.pages.lighthouse_results.thresholds.{$this->value}");
    }
}
