<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesDeleteRequests;
use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    use HandlesDeleteRequests;

    public function __construct(private AdminService $adminService)
    {
    }

    public function getStatistics(): JsonResponse
    {
        $stats = $this->adminService->getStatistics();
        return response()->json($stats);
    }

    public function deleteUser($userId): JsonResponse
    {
        return $this->deleteResource('user', $userId, fn (int $normalizedId) => $this->adminService->deleteUser($normalizedId));
    }

    public function deletePost($postId): JsonResponse
    {
        return $this->deleteResource('post', $postId, fn (int $normalizedId) => $this->adminService->deletePost($normalizedId));
    }

    public function deleteComment($commentId): JsonResponse
    {
        return $this->deleteResource('comment', $commentId, fn (int $normalizedId) => $this->adminService->deleteComment($normalizedId));
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $query = $request->query('q', '');
        $perPage = $request->query('per_page', 15);
        $users = $this->adminService->searchUsers($query, $perPage);
        return response()->json($users);
    }

    public function searchPosts(Request $request): JsonResponse
    {
        $query = $request->query('q', '');
        $perPage = $request->query('per_page', 15);
        $posts = $this->adminService->searchPosts($query, $perPage);
        return response()->json($posts);
    }

    public function getAllUsers(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $users = $this->adminService->getAllUsers($perPage);
        return response()->json($users);
    }

    public function getAllPosts(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $posts = $this->adminService->getAllPosts($perPage);
        return response()->json($posts);
    }

    public function getAllComments(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $comments = $this->adminService->getAllComments($perPage);
        return response()->json($comments);
    }

    public function getReports(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $status = $request->query('status');
        $query = $request->query('q');

        $reports = $this->adminService->getReports($perPage, $status, $query);

        return response()->json($reports);
    }

    public function resolveReport(Request $request, $reportId): JsonResponse
    {
        $status = $request->input('status', 'resolved');

        if (!in_array($status, ['resolved', 'dismissed'])) {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        try {
            $normalizedReportId = $this->normalizeResourceId($reportId);

            if ($normalizedReportId === null) {
                return response()->json(['message' => 'Invalid report ID'], 422);
            }

            $report = $this->adminService->resolveReport($normalizedReportId, $status, Auth::id());
            return response()->json(['message' => 'Report status updated successfully', 'report' => $report]);
        } catch (\DomainException $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        } catch (\Throwable $exception) {
            $this->logDeleteFailure('report', (int) $reportId, $exception, 'Admin report resolve failed');

            return response()->json(['message' => 'Failed to resolve report'], 500);
        }
    }

    public function deleteReport($reportId): JsonResponse
    {
        return $this->deleteResource('report', $reportId, fn (int $normalizedId) => $this->adminService->deleteReport($normalizedId));
    }
}
