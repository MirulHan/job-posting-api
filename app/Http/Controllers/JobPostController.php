<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Http\Requests\StoreJobPostRequest;
use Illuminate\Http\JsonResponse;

class JobPostController extends Controller
{
    /**
     * Store a newly created job post.
     *
     * @param  \App\Http\Requests\StoreJobPostRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreJobPostRequest $request): JsonResponse
    {
        try {
            // Process skills array if provided
            if ($request->has('skills') && is_array($request->skills)) {
                $skills = $request->skills;
            } else if ($request->has('skills') && is_string($request->skills)) {
                // Handle comma-separated skills if they come as a string
                $skills = array_map('trim', explode(',', $request->skills));
            } else {
                $skills = null;
            }

            // Create the job post
            $jobPost = JobPost::create([
                'title' => $request->title,
                'description' => $request->description,
                'company' => $request->company,
                'location' => $request->location,
                'job_type' => $request->job_type,
                'salary' => $request->salary,
                'contact_email' => $request->contact_email,
                'skills' => $skills,
                'application_deadline' => $request->application_deadline,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Job post created successfully',
                'data' => $jobPost
            ], 201);
        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Failed to create job post',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
