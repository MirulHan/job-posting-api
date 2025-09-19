<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Http\Requests\StoreJobPostRequest;
use App\Http\Resources\JobPostResource;
use App\Http\Resources\JobPostCollection;
use App\Services\JobPostService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostController extends Controller
{
    protected JobPostService $jobPostService;

    public function __construct(JobPostService $jobPostService)
    {
        $this->jobPostService = $jobPostService;
    }

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

            $jobPosts = $this->jobPostService->getPaginatedJobPosts($page, $perPage);

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
            $jobPost = $this->jobPostService->createJobPost($request->validated());

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
            $jobPost = $this->jobPostService->findJobPost($id);

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
