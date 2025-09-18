<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of job applications.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = JobApplication::with('jobPost');

        if ($request->has('job_post_id') && $request->job_post_id) {
            $query->where('job_post_id', $request->job_post_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $jobPosts = JobPost::all();

        $statuses = ['applied', 'screening', 'interview', 'offer', 'accepted', 'failed'];

        $applications = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('applications.index', compact('applications', 'jobPosts', 'statuses'));
    }

    /**
     * Display a specific job application.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $application = JobApplication::with('jobPost')->findOrFail($id);
        $statuses = ['applied', 'screening', 'interview', 'offer', 'accepted', 'failed'];

        return view('applications.show', compact('application', 'statuses'));
    }

    /**
     * Update the status of a job application.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:applied,screening,interview,offer,accepted,failed',
        ]);

        $application = JobApplication::findOrFail($id);
        $application->status = $request->status;
        $application->save();

        return redirect()->route('applications.show', $id)
            ->with('success', 'Application status updated successfully.');
    }
}
