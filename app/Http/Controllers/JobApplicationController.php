<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Http\Resources\JobApplicationCollection;
use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
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
            ]);

            $page = $validated['page'] ?? 1;
            $perPage = $validated['per_page'] ?? 15;

            $query = JobApplication::query()->with('jobPost');

            if (isset($validated['status'])) {
                $query->where('status', $validated['status']);
            }

            $query->orderBy('created_at', 'desc');

            $applications = $query->paginate($perPage, ['*'], 'page', $page);

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
            $application = JobApplication::with('jobPost')->findOrFail($id);

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
            $jobPost = JobPost::findOrFail($request->job_post_id);

            if (!$jobPost->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This job post is no longer accepting applications',
                ], 422);
            }

            if ($jobPost->application_deadline && now()->gt($jobPost->application_deadline)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The application deadline for this job has passed',
                ], 422);
            }

            $jobApplication = JobApplication::create([
                'job_post_id' => $request->job_post_id,
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'work_experience' => $request->work_experience,
                'status' => 'applied',
            ]);

            $jobApplication->load('jobPost');

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
