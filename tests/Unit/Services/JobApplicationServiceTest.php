<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\JobApplicationService;
use App\Models\JobApplication;
use App\Models\JobPost;

class JobApplicationServiceTest extends TestCase
{
    protected JobApplicationService $jobApplicationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jobApplicationService = new JobApplicationService();
    }

    public function test_get_available_statuses()
    {
        $statuses = $this->jobApplicationService->getAvailableStatuses();

        $expected = ['applied', 'screening', 'interview', 'offer', 'accepted', 'failed'];
        $this->assertEquals($expected, $statuses);
    }

    public function test_can_update_to_status_valid()
    {
        $application = new JobApplication(['status' => 'applied']);

        $this->assertTrue($this->jobApplicationService->canUpdateToStatus($application, 'screening'));
        $this->assertTrue($this->jobApplicationService->canUpdateToStatus($application, 'interview'));
    }

    public function test_can_update_to_status_from_accepted()
    {
        $application = new JobApplication(['status' => 'accepted']);

        $this->assertFalse($this->jobApplicationService->canUpdateToStatus($application, 'screening'));
        $this->assertFalse($this->jobApplicationService->canUpdateToStatus($application, 'failed'));
    }

    public function test_can_update_to_status_from_failed()
    {
        $application = new JobApplication(['status' => 'failed']);

        $this->assertFalse($this->jobApplicationService->canUpdateToStatus($application, 'screening'));
        $this->assertFalse($this->jobApplicationService->canUpdateToStatus($application, 'accepted'));
    }

    public function test_can_update_to_status_invalid_status()
    {
        $application = new JobApplication(['status' => 'applied']);

        $this->assertFalse($this->jobApplicationService->canUpdateToStatus($application, 'invalid_status'));
    }
}
