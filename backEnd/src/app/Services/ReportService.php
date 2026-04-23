<?php

namespace App\Services;

use App\Repositories\ReportRepository;

class ReportService
{
    public function __construct(private ReportRepository $reportRepository)
    {
    }

    public function getReports(int $perPage = 15, ?string $status = null, ?string $query = null)
    {
        return $this->reportRepository->paginate($perPage, $status, $query);
    }

    public function getPendingCount(): int
    {
        return $this->reportRepository->countPending();
    }

    public function resolveReport(int $reportId, string $status, ?int $reviewedBy = null)
    {
        $report = $this->reportRepository->findById($reportId);

        if (!$report) {
            throw new \DomainException('Report not found.');
        }

        return $this->reportRepository->updateStatus($reportId, $status, $reviewedBy);
    }

    public function deleteReport(int $reportId): bool
    {
        return $this->reportRepository->delete($reportId);
    }
}
