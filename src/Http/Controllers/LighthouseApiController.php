<?php

namespace FilamentSpatieLighthouse\Http\Controllers;

use FilamentSpatieLighthouse\Models\LighthouseAuditResult;
use FilamentSpatieLighthouse\ResultStores\ResultStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LighthouseApiController
{
    public function __construct(
        protected ResultStore $resultStore
    ) {
    }

    /**
     * Verify the secret token
     */
    protected function verifyToken(Request $request): bool
    {
        $secretToken = config('filament-spatie-lighthouse.endpoints.secret_token');
        
        if (empty($secretToken)) {
            return false;
        }

        $providedToken = $request->header('X-Lighthouse-Token') 
            ?? $request->query('token')
            ?? $request->bearerToken();

        return hash_equals($secretToken, $providedToken ?? '');
    }

    /**
     * Get latest audit results
     */
    public function latest(Request $request, ?string $url = null): JsonResponse
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $url = $url ?? $request->query('url');
        $results = $this->resultStore->latestResults($url);

        if (!$results) {
            return response()->json(['message' => 'No results found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'url' => $results->url,
            'performance_score' => $results->performanceScore(),
            'accessibility_score' => $results->accessibilityScore(),
            'best_practices_score' => $results->bestPracticesScore(),
            'seo_score' => $results->seoScore(),
            'finished_at' => $results->finishedAt?->toIso8601String(),
        ]);
    }

    /**
     * Get all audit results
     */
    public function index(Request $request): JsonResponse
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $url = $request->query('url');
        $limit = (int) ($request->query('limit', 10));
        $limit = min($limit, 100); // Max 100 results

        $query = LighthouseAuditResult::query()
            ->when($url, fn($q) => $q->where('url', $url))
            ->latest()
            ->limit($limit);

        $results = $query->get()->map(function ($result) {
            return [
                'id' => $result->id,
                'url' => $result->url,
                'performance_score' => $result->performance_score,
                'accessibility_score' => $result->accessibility_score,
                'best_practices_score' => $result->best_practices_score,
                'seo_score' => $result->seo_score,
                'finished_at' => $result->finished_at?->toIso8601String(),
                'created_at' => $result->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'data' => $results,
            'count' => $results->count(),
        ]);
    }

    /**
     * Get a specific audit result by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        if (!$this->verifyToken($request)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $result = LighthouseAuditResult::find($id);

        if (!$result) {
            return response()->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'id' => $result->id,
            'url' => $result->url,
            'performance_score' => $result->performance_score,
            'accessibility_score' => $result->accessibility_score,
            'best_practices_score' => $result->best_practices_score,
            'seo_score' => $result->seo_score,
            'finished_at' => $result->finished_at?->toIso8601String(),
            'created_at' => $result->created_at->toIso8601String(),
            'raw_results' => $result->raw_results,
        ]);
    }

    /**
     * Get health status
     */
    public function health(Request $request): JsonResponse
    {
        // Health endpoint doesn't require token
        $latestResult = LighthouseAuditResult::latest('finished_at')->first();

        return response()->json([
            'status' => 'ok',
            'latest_audit' => $latestResult ? [
                'url' => $latestResult->url,
                'finished_at' => $latestResult->finished_at?->toIso8601String(),
            ] : null,
        ]);
    }
}
