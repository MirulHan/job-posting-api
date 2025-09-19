<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class JobApplicationService
{
    /**
     * Get paginated job applications with optional filtering
     *
     * @param int $page
     * @param int $perPage
     * @param string|null $status
     * @param int|null $jobPostId
     * @return LengthAwarePaginator
     */
    public function getPaginatedApplications(
        int $page = 1,
        int $perPage = 15,
        ?string $status = null,
        ?int $jobPostId = null
    ): LengthAwarePaginator {
        $query = JobApplication::query()->with('jobPost');

        if ($status) {
            $query->where('status', $status);
        }

        if ($jobPostId) {
            $query->where('job_post_id', $jobPostId);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Find job application by ID with relations
     *
     * @param int $id
     * @return JobApplication
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findApplicationWithJobPost(int $id): JobApplication
    {
        return JobApplication::with('jobPost')->findOrFail($id);
    }

    /**
     * Create a new job application
     *
     * @param array $data
     * @return JobApplication
     */
    public function createApplication(array $data): JobApplication
    {
        $application = JobApplication::create([
            'job_post_id' => $data['job_post_id'],
            'full_name' => $data['full_name'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'] ?? null,
            'work_experience' => $data['work_experience'],
            'status' => 'applied',
        ]);

        $application->load('jobPost');

        return $application;
    }

    /**
     * Update application status
     *
     * @param JobApplication $application
     * @param string $status
     * @return JobApplication
     */
    public function updateStatus(JobApplication $application, string $status): JobApplication
    {
        $application->update(['status' => $status]);
        return $application->fresh();
    }

    /**
     * Get all available statuses
     *
     * @return array
     */
    public function getAvailableStatuses(): array
    {
        return ['applied', 'screening', 'interview', 'offer', 'accepted', 'failed'];
    }

    /**
     * Check if application can be updated to given status
     *
     * @param JobApplication $application
     * @param string $newStatus
     * @return bool
     */
    public function canUpdateToStatus(JobApplication $application, string $newStatus): bool
    {
        $currentStatus = $application->status;
        $validStatuses = $this->getAvailableStatuses();

        // Check if new status is valid
        if (!in_array($newStatus, $validStatuses)) {
            return false;
        }

        // If application is already accepted or failed, it cannot be changed
        if (in_array($currentStatus, ['accepted', 'failed'])) {
            return false;
        }

        return true;
    }

    /**
     * Get applications by job post
     *
     * @param int $jobPostId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getApplicationsByJobPost(int $jobPostId, int $perPage = 15): LengthAwarePaginator
    {
        return JobApplication::where('job_post_id', $jobPostId)
            ->with('jobPost')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get statistics for job applications
     *
     * @param int|null $jobPostId
     * @return array
     */
    public function getApplicationStatistics(?int $jobPostId = null): array
    {
        $query = JobApplication::query();

        if ($jobPostId) {
            $query->where('job_post_id', $jobPostId);
        }

        $total = $query->count();
        $statusCounts = $query->groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total' => $total,
            'by_status' => $statusCounts,
        ];
    }
}
