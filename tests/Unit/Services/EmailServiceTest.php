<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EmailService;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Mail\JobApplicationSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailServiceTest extends TestCase
{
    protected EmailService $emailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emailService = new EmailService();
    }

    public function test_sends_email_when_mock_mode_disabled()
    {
        Mail::fake();

        $this->emailService->disableMockMode();

        $jobPost = new JobPost([
            'id' => 1,
            'title' => 'Software Engineer',
            'company' => 'Tech Corp'
        ]);

        $application = new JobApplication([
            'id' => 1,
            'full_name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        $application->created_at = now();
        $application->setRelation('jobPost', $jobPost);

        $result = $this->emailService->sendJobApplicationConfirmation($application);

        $this->assertTrue($result);
        Mail::assertSent(JobApplicationSubmitted::class, function ($mail) use ($application) {
            return $mail->hasTo('john@example.com');
        });
    }

    public function test_mocks_email_when_mock_mode_enabled()
    {
        $this->emailService->enableMockMode();
        $this->emailService->clearMockEmails();

        $jobPost = new JobPost([
            'id' => 1,
            'title' => 'Software Engineer',
            'company' => 'Tech Corp'
        ]);

        $application = new JobApplication([
            'id' => 1,
            'full_name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        $application->created_at = now();
        $application->setRelation('jobPost', $jobPost);

        $result = $this->emailService->sendJobApplicationConfirmation($application);

        $this->assertTrue($result);

        $mockEmails = $this->emailService->getMockEmails();
        $this->assertCount(1, $mockEmails);
        $this->assertEquals('john@example.com', $mockEmails[0]['to']);
        $this->assertEquals('John Doe', $mockEmails[0]['applicant_name']);
        $this->assertEquals('Software Engineer', $mockEmails[0]['job_title']);
    }

    public function test_skips_email_when_no_email_provided()
    {
        $jobPost = new JobPost([
            'id' => 1,
            'title' => 'Software Engineer',
            'company' => 'Tech Corp'
        ]);

        $application = new JobApplication([
            'id' => 1,
            'full_name' => 'John Doe',
            'email' => null
        ]);
        $application->created_at = now();
        $application->setRelation('jobPost', $jobPost);

        $result = $this->emailService->sendJobApplicationConfirmation($application);

        $this->assertFalse($result);
    }

    public function test_can_clear_mock_emails()
    {
        $this->emailService->enableMockMode();
        $this->emailService->clearMockEmails(); // Clear any existing emails first

        $jobPost = new JobPost([
            'id' => 1,
            'title' => 'Software Engineer',
            'company' => 'Tech Corp'
        ]);

        $application = new JobApplication([
            'id' => 1,
            'full_name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        $application->created_at = now();
        $application->setRelation('jobPost', $jobPost);

        $this->emailService->sendJobApplicationConfirmation($application);
        $this->assertCount(1, $this->emailService->getMockEmails());

        $this->emailService->clearMockEmails();
        $this->assertCount(0, $this->emailService->getMockEmails());
    }

    public function test_mock_mode_status_methods()
    {
        $this->emailService->enableMockMode();
        $this->assertTrue($this->emailService->isMockMode());

        $this->emailService->disableMockMode();
        $this->assertFalse($this->emailService->isMockMode());
    }
}
