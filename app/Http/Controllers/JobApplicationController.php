<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Http\Resources\JobApplicationCollection;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Services\JobApplicationService;
use App\Services\JobPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    protected JobApplicationService $jobApplicationService;
    protected JobPostService $jobPostService;

    public function __construct(
        JobApplicationService $jobApplicationService,
        JobPostService $jobPostService
    ) {
        $this->jobApplicationService = $jobApplicationService;
        $this->jobPostService = $jobPostService;
    }

    /**
     * Display a listing of job applications.
     *
     * @param Request $request
     * @return JobApplicationCollection|JsonResponse
     */
    public function index(Request $request): JobApplicationCollection|JsonResponse
    {
        try {
            $validated = $request->validate([
                // @example 1
                'page' => 'nullable|integer|min:1',
                // @example 15
                'per_page' => 'nullable|integer|min:1|max:100',
                // @example 1
                'status' => 'nullable|in:applied,screening,interview,offer,accepted,failed',
                'job_post_id' => 'nullable|integer|exists:job_posts,id',
            ]);

            $page = $validated['page'] ?? 1;
            $perPage = $validated['per_page'] ?? 15;
            $status = $validated['status'] ?? null;
            $jobPostId = $validated['job_post_id'] ?? null;

            $applications = $this->jobApplicationService->getPaginatedApplications(
                $page,
                $perPage,
                $status,
                $jobPostId
            );

            return new JobApplicationCollection($applications);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job applications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified job application.
     *
     * @param int $id
     * @return JobApplicationResource|JsonResponse
     */
    public function show(int $id): JobApplicationResource|JsonResponse
    {
        try {
            $application = $this->jobApplicationService->findApplicationWithJobPost($id);

            return (new JobApplicationResource($application))
                ->additional(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job application not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Store a new job application.
     *
     * @param StoreJobApplicationRequest $request
     * @return JobApplicationResource|JsonResponse
     */
    public function store(StoreJobApplicationRequest $request): JobApplicationResource|JsonResponse
    {
        try {
            $jobPost = $this->jobPostService->findJobPost($request->job_post_id);

            // Check if job post is accepting applications
            $validationError = $this->jobPostService->getApplicationValidationError($jobPost);
            if ($validationError) {
                return response()->json([
                    'success' => false,
                    'message' => $validationError,
                ], 422);
            }

            $jobApplication = $this->jobApplicationService->createApplication($request->validated());

            return (new JobApplicationResource($jobApplication))
                ->additional([
                    'success' => true,
                    'message' => 'Your job application has been submitted successfully',
                ])
                ->response();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit job application',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
