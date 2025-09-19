<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobPost;
use App\Models\JobApplication;
use App\Services\EmailService;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {--mock : Use mock mode} {--clear : Clear mock emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email functionality for job applications';

    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear')) {
            $this->emailService->clearMockEmails();
            $this->info('Mock emails cleared!');
            return;
        }

        if ($this->option('mock')) {
            $this->emailService->enableMockMode();
            $this->info('Mock mode enabled for this test.');
        }

        $this->info('Testing Email Functionality');
        $this->line('============================');

        $jobPost = JobPost::first();
        if (!$jobPost) {
            $this->error('No job posts found. Please create a job post first or run the seeders.');
            return;
        }

        $this->info("Using Job Post: {$jobPost->title} at {$jobPost->company}");

        $testApplication = JobApplication::create([
            'job_post_id' => $jobPost->id,
            'full_name' => 'Test User',
            'phone_number' => '+1234567890',
            'email' => 'test@example.com',
            'work_experience' => 'This is a test application created by the email test command.',
            'status' => 'applied'
        ]);

        $testApplication->load('jobPost');

        $this->info("Created test application with ID: {$testApplication->id}");

        $result = $this->emailService->sendJobApplicationConfirmation($testApplication);

        if ($result) {
            $this->info('Email sent successfully!');

            if ($this->emailService->isMockMode()) {
                $this->showMockEmails();
            }
        } else {
            $this->error('Failed to send email.');
        }

        $testApplication->delete();
        $this->info('Test application cleaned up.');
    }

    protected function showMockEmails()
    {
        $mockEmails = $this->emailService->getMockEmails();

        $this->line('');
        $this->info('Mock Emails Sent:');
        $this->line('-------------------');

        foreach ($mockEmails as $index => $email) {
            $this->line("Email #" . ($index + 1));
            $this->line("To: {$email['to']}");
            $this->line("Subject: {$email['subject']}");
            $this->line("Applicant: {$email['applicant_name']}");
            $this->line("Job: {$email['job_title']} at {$email['company']}");
            $this->line("Application ID: #{$email['application_id']}");
            $this->line("Sent At: {$email['sent_at']}");
            $this->line("Status: {$email['status']}");
            $this->line('');
        }

        $this->info("Total mock emails: " . count($mockEmails));
        $this->line('');
        $this->comment('💡 Mock emails are stored in: storage/logs/mock_emails.json');
        $this->comment('💡 Use --clear flag to clear mock emails: php artisan email:test --clear');
    }
}
