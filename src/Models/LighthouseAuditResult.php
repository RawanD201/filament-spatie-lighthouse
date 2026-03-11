<?php

namespace FilamentSpatieLighthouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;

/**
 * @property int $id
 * @property string $url
 * @property array|null $raw_results
 * @property string|null $raw_result_path
 * @property array $scores
 * @property int|null $performance_score
 * @property int|null $accessibility_score
 * @property int|null $best_practices_score
 * @property int|null $seo_score
 * @property \Carbon\Carbon $finished_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class LighthouseAuditResult extends Model
{
    use HasFactory;
    use MassPrunable;

    protected $fillable = [
        'url',
        'raw_results',
        'raw_result_path',
        'scores',
        'performance_score',
        'accessibility_score',
        'best_practices_score',
        'seo_score',
        'finished_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'raw_results' => 'array',
        'scores' => 'array',
        'finished_at' => 'datetime',
    ];

    public function prunable()
    {
        $days = config('filament-spatie-lighthouse.keep_history_for_days', 30);

        return static::where('created_at', '<=', now()->subDays($days));
    }
}
