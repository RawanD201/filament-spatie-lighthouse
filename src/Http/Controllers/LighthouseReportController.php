<?php

namespace FilamentSpatieLighthouse\Http\Controllers;

use FilamentSpatieLighthouse\Models\LighthouseAuditResult;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Spatie\Lighthouse\LighthouseResult;

class LighthouseReportController extends Controller
{
    public function show($id)
    {
        abort_unless(auth()->check(), 403);
        $record = LighthouseAuditResult::findOrFail($id);
        $rawResults = $this->resolveRawResults($record);

        if (empty($rawResults)) {
            abort(404, 'Report not found');
        }

        $result = new LighthouseResult($rawResults);

        return response($result->html())
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="lighthouse-report.html"');
    }

    public function download($id)
    {
        abort_unless(auth()->check(), 403);
        $record = LighthouseAuditResult::findOrFail($id);
        $rawResults = $this->resolveRawResults($record);

        if (empty($rawResults)) {
            abort(404, 'Report not found');
        }

        $result = new LighthouseResult($rawResults);
        $filename = 'lighthouse-report-' . md5($record->url) . '-' . now()->format('Y-m-d-His') . '.html';

        return response()->streamDownload(function () use ($result) {
            echo $result->html();
        }, $filename, [
            'Content-Type' => 'text/html',
        ]);
    }

    protected function resolveRawResults(LighthouseAuditResult $record): array
    {
        if ($record->raw_result_path) {
            $disk = config('filament-spatie-lighthouse.raw_results_disk', 'local');
            $json = Storage::disk($disk)->get($record->raw_result_path);

            return $json ? json_decode($json, true) : [];
        }

        return $record->raw_results ?? [];
    }
}
