<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait HandlesDeleteRequests
{
    protected function normalizeResourceId(mixed $resourceId): ?int
    {
        $normalizedId = filter_var($resourceId, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        return $normalizedId === false ? null : (int) $normalizedId;
    }

    protected function deleteResource(string $resourceLabel, mixed $resourceId, callable $deleter): JsonResponse
    {
        $normalizedId = $this->normalizeResourceId($resourceId);

        if ($normalizedId === null) {
            return response()->json(['message' => 'Invalid ' . $resourceLabel . ' ID'], 422);
        }

        try {
            $deleted = $deleter($normalizedId);

            if (!$deleted) {
                return response()->json(['message' => ucfirst($resourceLabel) . ' not found'], 404);
            }

            return response()->json(['message' => ucfirst($resourceLabel) . ' deleted successfully']);
        } catch (QueryException $exception) {
            $this->logDeleteFailure($resourceLabel, $normalizedId, $exception, ucfirst($resourceLabel) . ' delete failed');

            return response()->json(['message' => 'Unable to delete ' . $resourceLabel . ' right now.'], 409);
        } catch (\Throwable $exception) {
            $this->logDeleteFailure($resourceLabel, $normalizedId, $exception, ucfirst($resourceLabel) . ' delete failed');

            return response()->json(['message' => 'Failed to delete ' . $resourceLabel], 500);
        }
    }

    protected function logDeleteFailure(string $resourceLabel, int $resourceId, \Throwable $exception, string $message): void
    {
        Log::error($message, [
            'resource' => $resourceLabel,
            'resource_id' => $resourceId,
            'exception' => $exception::class,
            'exception_message' => $exception->getMessage(),
        ]);
    }
}
