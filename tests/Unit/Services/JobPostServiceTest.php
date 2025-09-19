<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\JobPostService;
use App\Models\JobPost;
use Carbon\Carbon;

class JobPostServiceTest extends TestCase
{
    protected JobPostService $jobPostService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jobPostService = new JobPostService();
    }

    public function test_process_skills_with_array()
    {
        $skills = ['PHP', 'Laravel', 'JavaScript'];

        $result = $this->jobPostService->processSkills($skills);

        $this->assertEquals(['PHP', 'Laravel', 'JavaScript'], $result);
    }

    public function test_process_skills_with_string()
    {
        $skills = 'PHP, Laravel, JavaScript';

        $result = $this->jobPostService->processSkills($skills);

        $this->assertEquals(['PHP', 'Laravel', 'JavaScript'], $result);
    }

    public function test_process_skills_with_empty_input()
    {
        $this->assertNull($this->jobPostService->processSkills(null));
        $this->assertNull($this->jobPostService->processSkills(''));
        $this->assertNull($this->jobPostService->processSkills([]));
    }

    public function test_is_accepting_applications_active_job()
    {
        $jobPost = new JobPost([
            'is_active' => true,
            'application_deadline' => Carbon::now()->addDays(30),
        ]);

        $this->assertTrue($this->jobPostService->isAcceptingApplications($jobPost));
    }

    public function test_is_accepting_applications_inactive_job()
    {
        $jobPost = new JobPost([
            'is_active' => false,
        ]);

        $this->assertFalse($this->jobPostService->isAcceptingApplications($jobPost));
    }

    public function test_is_accepting_applications_expired_deadline()
    {
        $jobPost = new JobPost([
            'is_active' => true,
            'application_deadline' => Carbon::now()->subDays(1),
        ]);

        $this->assertFalse($this->jobPostService->isAcceptingApplications($jobPost));
    }

    public function test_get_application_validation_error_inactive()
    {
        $jobPost = new JobPost(['is_active' => false]);

        $error = $this->jobPostService->getApplicationValidationError($jobPost);

        $this->assertEquals('This job post is no longer accepting applications', $error);
    }

    public function test_get_application_validation_error_expired()
    {
        $jobPost = new JobPost([
            'is_active' => true,
            'application_deadline' => Carbon::now()->subDays(1),
        ]);

        $error = $this->jobPostService->getApplicationValidationError($jobPost);

        $this->assertEquals('The application deadline for this job has passed', $error);
    }

    public function test_get_application_validation_error_valid()
    {
        $jobPost = new JobPost([
            'is_active' => true,
            'application_deadline' => Carbon::now()->addDays(30),
        ]);

        $error = $this->jobPostService->getApplicationValidationError($jobPost);

        $this->assertNull($error);
    }
}
