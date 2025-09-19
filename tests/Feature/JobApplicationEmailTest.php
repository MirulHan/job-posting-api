<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\JobPost;
use App\Services\EmailService;
use Carbon\Carbon;

class JobApplicationEmailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure we're in mock mode for testing
        config(['mail.mock_mode' => true]);

        // Clear any existing mock emails
        app(EmailService::class)->clearMockEmails();
    }

    public function test_sends_email_when_application_submitted_with_email()
    {
        $jobPost = JobPost::factory()->create([
            'is_active' => true,
            'application_deadline' => Carbon::now()->addDays(30)
        ]);

        $applicationData = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Jane Smith',
            'phone_number' => '+1234567890',
            'email' => 'jane@example.com',
            'work_experience' => '3 years of software development experience'
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);

        $response->assertStatus(201);

        // Check that mock email was sent
        $emailService = app(EmailService::class);
        $mockEmails = $emailService->getMockEmails();

        $this->assertCount(1, $mockEmails);
        $this->assertEquals('jane@example.com', $mockEmails[0]['to']);
        $this->assertEquals('Jane Smith', $mockEmails[0]['applicant_name']);
        $this->assertEquals($jobPost->title, $mockEmails[0]['job_title']);
        $this->assertEquals($jobPost->company, $mockEmails[0]['company']);
        $this->assertEquals('job_application_confirmation', $mockEmails[0]['type']);
    }

    public function test_does_not_send_email_when_application_submitted_without_email()
    {
        $jobPost = JobPost::factory()->create([
            'is_active' => true,
            'application_deadline' => Carbon::now()->addDays(30)
        ]);

        $applicationData = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'John Doe',
            'phone_number' => '+1234567890',
            // No email provided
            'work_experience' => '2 years of experience'
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);

        $response->assertStatus(201);

        // Check that no mock email was sent
        $emailService = app(EmailService::class);
        $mockEmails = $emailService->getMockEmails();

        $this->assertCount(0, $mockEmails);
    }

    public function test_multiple_applications_send_multiple_emails()
    {
        $jobPost = JobPost::factory()->create([
            'is_active' => true,
            'application_deadline' => Carbon::now()->addDays(30)
        ]);

        // First application with email
        $applicationData1 = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Alice Johnson',
            'phone_number' => '+1234567890',
            'email' => 'alice@example.com',
            'work_experience' => '5 years of experience'
        ];

        // Second application with email
        $applicationData2 = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Bob Wilson',
            'phone_number' => '+0987654321',
            'email' => 'bob@example.com',
            'work_experience' => '3 years of experience'
        ];

        $this->postJson('/api/job-applications', $applicationData1);
        $this->postJson('/api/job-applications', $applicationData2);

        // Check that both mock emails were sent
        $emailService = app(EmailService::class);
        $mockEmails = $emailService->getMockEmails();

        $this->assertCount(2, $mockEmails);

        $this->assertEquals('alice@example.com', $mockEmails[0]['to']);
        $this->assertEquals('Alice Johnson', $mockEmails[0]['applicant_name']);

        $this->assertEquals('bob@example.com', $mockEmails[1]['to']);
        $this->assertEquals('Bob Wilson', $mockEmails[1]['applicant_name']);
    }

    public function test_email_contains_correct_application_details()
    {
        $jobPost = JobPost::factory()->create([
            'title' => 'Senior Developer',
            'company' => 'Amazing Tech Co',
            'is_active' => true,
            'application_deadline' => Carbon::now()->addDays(30)
        ]);

        $applicationData = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Emma Davis',
            'phone_number' => '+1234567890',
            'email' => 'emma@example.com',
            'work_experience' => '4 years of full-stack development'
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);
        $response->assertStatus(201);

        $emailService = app(EmailService::class);
        $mockEmails = $emailService->getMockEmails();

        $this->assertCount(1, $mockEmails);

        $email = $mockEmails[0];
        $this->assertEquals('emma@example.com', $email['to']);
        $this->assertEquals('Emma Davis', $email['applicant_name']);
        $this->assertEquals('Senior Developer', $email['job_title']);
        $this->assertEquals('Amazing Tech Co', $email['company']);
        $this->assertArrayHasKey('application_id', $email);
        $this->assertArrayHasKey('submission_date', $email);
        $this->assertEquals('job_application_confirmation', $email['type']);
        $this->assertStringContainsString('Job Application Confirmation - Senior Developer', $email['subject']);
    }
}
