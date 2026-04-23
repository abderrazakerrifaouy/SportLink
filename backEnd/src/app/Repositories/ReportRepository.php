<?php

namespace App\Repositories;

use App\Models\Report;

class ReportRepository
{
    public function paginate(int $perPage = 15, ?string $status = null, ?string $query = null)
    {
        return Report::with(['reporter', 'reviewer', 'reportable'])
            ->when($status, fn ($builder) => $builder->where('status', $status))
            ->when($query, function ($builder) use ($query) {
                $builder->where('reason', 'like', '%' . $query . '%');
            })
            ->latest()
            ->paginate($perPage);
    }

    public function countPending(): int
    {
        return Report::where('status', 'pending')->count();
    }

    public function findById(int $id): ?Report
    {
        return Report::with(['reporter', 'reviewer', 'reportable'])->find($id);
    }

    public function updateStatus(int $id, string $status, ?int $reviewedBy = null): ?Report
    {
        $report = Report::find($id);

        if (!$report) {
            return null;
        }

        $report->update([
            'status' => $status,
            'reviewed_by' => $reviewedBy,
            'resolved_at' => now(),
        ]);

        return $report->fresh()->load(['reporter', 'reviewer', 'reportable']);
    }

    public function delete(int $id): bool
    {
        $report = Report::find($id);

        if (!$report) {
            return false;
        }

        return (bool) $report->delete();
    }
}
