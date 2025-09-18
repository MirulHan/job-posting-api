<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\JsonResponse;

class JobApplicationController extends Controller
{
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
