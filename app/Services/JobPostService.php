<?php

namespace App\Services;

use App\Models\JobPost;
use App\Http\Resources\JobPostCollection;
use App\Http\Resources\JobPostResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class JobPostService
{
    /**
     * Get paginated job posts
     *
     * @param int $page
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedJobPosts(int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        return JobPost::query()->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Find job post by ID
     *
     * @param int $id
     * @return JobPost
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findJobPost(int $id): JobPost
    {
        return JobPost::findOrFail($id);
    }

    /**
     * Create a new job post
     *
     * @param array $data
     * @return JobPost
     */
    public function createJobPost(array $data): JobPost
    {
        $skills = $this->processSkills($data['skills'] ?? null);

        return JobPost::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'company' => $data['company'],
            'location' => $data['location'],
            'job_type' => $data['job_type'],
            'salary' => $data['salary'],
            'contact_email' => $data['contact_email'],
            'skills' => $skills,
            'application_deadline' => $data['application_deadline'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Process skills input (string or array) into array format
     *
     * @param mixed $skills
     * @return array|null
     */
    public function processSkills($skills): ?array
    {
        if (empty($skills)) {
            return null;
        }

        $processedSkills = collect($skills)
            ->when(is_string($skills), function ($collection) use ($skills) {
                return collect(explode(',', $skills));
            })
            ->filter()
            ->map(function ($skill) {
                return trim($skill);
            })
            ->values()
            ->toArray();

        return !empty($processedSkills) ? $processedSkills : null;
    }

    /**
     * Check if job post is accepting applications
     *
     * @param JobPost $jobPost
     * @return bool
     */
    public function isAcceptingApplications(JobPost $jobPost): bool
    {
        if (!$jobPost->is_active) {
            return false;
        }

        if ($jobPost->application_deadline && now()->gt($jobPost->application_deadline)) {
            return false;
        }

        return true;
    }

    /**
     * Get job post validation error message
     *
     * @param JobPost $jobPost
     * @return string|null
     */
    public function getApplicationValidationError(JobPost $jobPost): ?string
    {
        if (!$jobPost->is_active) {
            return 'This job post is no longer accepting applications';
        }

        if ($jobPost->application_deadline && now()->gt($jobPost->application_deadline)) {
            return 'The application deadline for this job has passed';
        }

        return null;
    }
}
