<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Trait for controllers that support both HTML and JSON responses
 *
 * Usage:
 * 1. Add 'use RespondsWithJson;' in your controller
 * 2. Call $this->respondWithJson($request, $data, $view, $viewData)
 *
 * Supports:
 * - URL with .json extension (e.g., /users/123/article.json)
 * - Accept: application/json header
 */
trait RespondsWithJson
{
    /**
     * Check if request expects JSON response
     */
    protected function wantsJson(Request $request): bool
    {
        if ($request->wantsJson()) {
            return true;
        }

        return (bool) $this->hasJsonExtension($request);
    }

    /**
     * Check if request path ends with .json
     */
    protected function hasJsonExtension(Request $request): bool
    {
        return str_ends_with($request->path(), '.json');
    }

    /**
     * Remove .json extension from a parameter value
     */
    protected function removeJsonExtension(string $value): string
    {
        if (str_ends_with($value, '.json')) {
            return substr($value, 0, -5);
        }

        return $value;
    }

    /**
     * Return either JSON or HTML response based on request
     *
     * @param  Request  $request  The request instance
     * @param  mixed  $data  Data to return as JSON (JsonResource, array, or model)
     * @param  view-string  $view  View name for HTML response
     * @param  array<string, mixed>  $viewData  Additional data for view
     */
    protected function respondWithJson(
        Request $request,
        mixed $data,
        string $view,
        array $viewData = []
    ): JsonResponse|View {
        if ($this->wantsJson($request)) {
            return $this->jsonResponse($data);
        }

        return view($view, array_merge($viewData, $this->getViewDataKey($data)));
    }

    /**
     * Create JSON response from data
     */
    protected function jsonResponse(mixed $data): JsonResponse
    {
        return response()->json($data);
    }

    /**
     * Get view data key based on data type
     *
     * @return array<string, mixed>
     */
    protected function getViewDataKey(mixed $data): array
    {
        if ($data instanceof JsonResource) {
            // Use resource class name as key (e.g., ArticleShow -> 'article')
            $className = class_basename($data::class);
            $key = strtolower((string) preg_replace('/([a-z])([A-Z])/', '$1_$2', $className));
            $key = str_replace('_show', '', $key);
            $key = str_replace('_resource', '', $key);

            return [$key => $data];
        }

        return [];
    }
}
