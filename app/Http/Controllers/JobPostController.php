<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Http\Requests\StoreJobPostRequest;
use App\Http\Resources\JobPostResource;
use App\Http\Resources\JobPostCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostController extends Controller
{
    /**
     * Get a list of job posts.
     *
     * @param Request $request
     * @return JobPostCollection|JsonResponse
     */
    public function index(Request $request): JobPostCollection|JsonResponse
    {
        try {
            $validated = $request->validate([
                // @example 1
                'page' => 'nullable|integer|min:1',
                // @example 15
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $page = $validated['page'] ?? 1;
            $perPage = $validated['per_page'] ?? 15;

            $query = JobPost::query();
            $jobPosts = $query->paginate($perPage, ['*'], 'page', $page);

            return new JobPostCollection($jobPosts);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve job posts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store job post.
     *
     * Store a newly created job post.
     *
     * @param StoreJobPostRequest $request
     * @return JsonResponse
     */
    public function store(StoreJobPostRequest $request): JsonResponse
    {
        try {
            $skills = collect($request->skills)
                ->when(is_string($request->skills), function ($collection) use ($request) {
                    return collect(explode(',', $request->skills));
                })
                ->filter()
                ->map(function ($skill) {
                    return trim($skill);
                })
                ->values()
                ->toArray();

            $skills = !empty($skills) ? $skills : null;

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

            return (new JobPostResource($jobPost))
                ->additional(['success' => true, 'message' => 'Job post created successfully'])
                ->response();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create job post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified job post.
     *
     * @param int $id
     * @return JobPostResource|JsonResponse
     */
    public function show(int $id): JobPostResource|JsonResponse
    {
        try {
            $jobPost = JobPost::findOrFail($id);

            return (new JobPostResource($jobPost))
                ->additional(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job post not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
}
