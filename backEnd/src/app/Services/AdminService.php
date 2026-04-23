<?php

namespace App\Services;

use App\Models\ClubAdmin;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Repositories\CommentRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;

class AdminService
{
    public function __construct(
        private UserRepository $userRepository,
        private PostRepository $postRepository,
        private CommentRepository $commentRepository,
        private ReportService $reportService,
    ) {
    }

    public function getStatistics(): array
    {
        return [
            'totalUsers' => User::count(),
            'totalPosts' => Post::count(),
            'totalComments' => Comment::count(),
            'totalPlayers' => User::whereRaw('LOWER(role) LIKE ? OR LOWER(role) LIKE ?', ['%player%', '%joueur%'])->count(),
            'totalClubAdmins' => ClubAdmin::count(),
            'pendingReports' => $this->reportService->getPendingCount(),
        ];
    }

    public function getAllUsers(int $perPage = 15)
    {
        return $this->userRepository->paginate($perPage);
    }

    public function searchUsers(string $query, int $perPage = 15)
    {
        return $this->userRepository->searchByNameWithPagination($query, $perPage);
    }

    public function deleteUser(int $userId): bool
    {
        return $this->userRepository->delete($userId);
    }

    public function getAllPosts(int $perPage = 15)
    {
        return $this->postRepository->paginate($perPage);
    }

    public function searchPosts(string $query, int $perPage = 15)
    {
        return $this->postRepository->searchByContentWithPagination($query, $perPage);
    }

    public function deletePost(int $postId): bool
    {
        return $this->postRepository->deletePost($postId);
    }

    public function getAllComments(int $perPage = 15)
    {
        return $this->commentRepository->paginate($perPage);
    }

    public function deleteComment(int $commentId): bool
    {
        return $this->commentRepository->deleteById($commentId);
    }

    public function getReports(int $perPage = 15, ?string $status = null, ?string $query = null)
    {
        return $this->reportService->getReports($perPage, $status, $query);
    }

    public function resolveReport(int $reportId, string $status, ?int $reviewedBy = null)
    {
        return $this->reportService->resolveReport($reportId, $status, $reviewedBy);
    }

    public function deleteReport(int $reportId): bool
    {
        return $this->reportService->deleteReport($reportId);
    }
}
