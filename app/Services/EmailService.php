<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Mail\JobApplicationSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    protected bool $mockMode;

    public function __construct()
    {
        // Enable mock mode based on environment or config
        $this->mockMode = config('mail.mock_mode', env('MAIL_MOCK_MODE', false));
    }

    /**
     * Send job application confirmation email
     *
     * @param JobApplication $jobApplication
     * @return bool
     */
    public function sendJobApplicationConfirmation(JobApplication $jobApplication): bool
    {
        // Only send email if applicant provided an email address
        if (!$jobApplication->email) {
            Log::info('Job application confirmation email skipped - no email provided', [
                'application_id' => $jobApplication->id,
                'applicant_name' => $jobApplication->full_name
            ]);
            return false;
        }

        try {
            if ($this->mockMode) {
                return $this->mockSendEmail($jobApplication);
            } else {
                return $this->realSendEmail($jobApplication);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send job application confirmation email', [
                'application_id' => $jobApplication->id,
                'email' => $jobApplication->email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send real email
     *
     * @param JobApplication $jobApplication
     * @return bool
     */
    protected function realSendEmail(JobApplication $jobApplication): bool
    {
        Mail::to($jobApplication->email)
            ->send(new JobApplicationSubmitted($jobApplication));

        Log::info('Job application confirmation email sent', [
            'application_id' => $jobApplication->id,
            'email' => $jobApplication->email,
            'applicant_name' => $jobApplication->full_name,
            'job_title' => $jobApplication->jobPost->title
        ]);

        return true;
    }

    /**
     * Mock email sending for testing/development
     *
     * @param JobApplication $jobApplication
     * @return bool
     */
    protected function mockSendEmail(JobApplication $jobApplication): bool
    {
        // Simulate email sending
                $emailData = [
            'to' => $jobApplication->email,
            'subject' => 'Job Application Confirmation - ' . $jobApplication->jobPost->title,
            'sent_at' => now()->toDateTimeString(),
            'applicant_name' => $jobApplication->full_name,
            'job_title' => $jobApplication->jobPost->title,
            'company' => $jobApplication->jobPost->company,
            'application_id' => $jobApplication->id,
            'submission_date' => ($jobApplication->created_at ?? now())->format('F d, Y'),
            'status' => 'MOCKED - Email would be sent in production'
        ];

        Log::info('MOCK: Job application confirmation email', $emailData);

        // Store mock email in log file for testing purposes
        $this->storeMockEmail($emailData);

        return true;
    }

    /**
     * Store mock email data for testing verification
     *
     * @param array $emailData
     * @return void
     */
    protected function storeMockEmail(array $emailData): void
    {
        $mockEmailsPath = storage_path('logs/mock_emails.json');

        $existingEmails = [];
        if (file_exists($mockEmailsPath)) {
            $existingEmails = json_decode(file_get_contents($mockEmailsPath), true) ?? [];
        }

        $existingEmails[] = array_merge($emailData, [
            'sent_at' => now()->toISOString(),
            'type' => 'job_application_confirmation'
        ]);

        file_put_contents($mockEmailsPath, json_encode($existingEmails, JSON_PRETTY_PRINT));
    }

    /**
     * Get all mock emails for testing verification
     *
     * @return array
     */
    public function getMockEmails(): array
    {
        $mockEmailsPath = storage_path('logs/mock_emails.json');

        if (!file_exists($mockEmailsPath)) {
            return [];
        }

        return json_decode(file_get_contents($mockEmailsPath), true) ?? [];
    }

    /**
     * Clear mock emails (useful for testing)
     *
     * @return void
     */
    public function clearMockEmails(): void
    {
        $mockEmailsPath = storage_path('logs/mock_emails.json');

        if (file_exists($mockEmailsPath)) {
            unlink($mockEmailsPath);
        }
    }

    /**
     * Check if mock mode is enabled
     *
     * @return bool
     */
    public function isMockMode(): bool
    {
        return $this->mockMode;
    }

    /**
     * Enable mock mode
     *
     * @return void
     */
    public function enableMockMode(): void
    {
        $this->mockMode = true;
    }

    /**
     * Disable mock mode
     *
     * @return void
     */
    public function disableMockMode(): void
    {
        $this->mockMode = false;
    }
}
