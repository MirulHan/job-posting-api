<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\JobPost;
use App\Models\JobApplication;
use Carbon\Carbon;

class JobApplicationControllerTest extends TestCase
{
    public function test_index_with_job_post_filter()
    {
        $jobPost1 = JobPost::factory()->create();
        $jobPost2 = JobPost::factory()->create();

        JobApplication::factory()->count(8)->create(['job_post_id' => $jobPost1->id]);
        JobApplication::factory()->count(3)->create(['job_post_id' => $jobPost2->id]);

        $response = $this->getJson("/api/job-applications?job_post_id={$jobPost1->id}");

        $response->assertStatus(200);

        $applications = $response->json('data');
        $this->assertEquals(8, count($applications));

        foreach ($applications as $application) {
            $this->assertEquals($jobPost1->id, $application['job_post']['id']);
        }
    }

    public function test_show_returns_application_with_job_post()
    {
        $jobPost = JobPost::factory()->create(['title' => 'Test Job']);
        $application = JobApplication::factory()->create([
            'job_post_id' => $jobPost->id,
            'full_name' => 'John Doe',
            'status' => 'applied'
        ]);

        $response = $this->getJson("/api/job-applications/{$application->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'full_name',
                    'phone_number',
                    'email',
                    'work_experience',
                    'status',
                    'job_post' => [
                        'id',
                        'title',
                        'company'
                    ]
                ],
                'success'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'full_name' => 'John Doe',
                    'status' => 'applied',
                    'job_post' => [
                        'title' => 'Test Job'
                    ]
                ]
            ]);
    }

    public function test_show_application_not_found()
    {
        $response = $this->getJson('/api/job-applications/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Job application not found'
            ]);
    }

    public function test_store_creates_application_successfully()
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

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'full_name',
                    'phone_number',
                    'email',
                    'work_experience',
                    'status',
                    'job_post'
                ],
                'success',
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Your job application has been submitted successfully',
                'data' => [
                    'full_name' => 'Jane Smith',
                    'phone_number' => '+1234567890',
                    'email' => 'jane@example.com',
                    'status' => 'applied'
                ]
            ]);

        $this->assertDatabaseHas('job_applications', [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Jane Smith',
            'phone_number' => '+1234567890',
            'status' => 'applied'
        ]);
    }

    public function test_store_validation_errors()
    {
        $response = $this->postJson('/api/job-applications', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'job_post_id',
                'full_name',
                'phone_number',
                'work_experience'
            ]);
    }

    public function test_store_with_invalid_job_post_id()
    {
        $applicationData = [
            'job_post_id' => 999,
            'full_name' => 'Jane Smith',
            'phone_number' => '+1234567890',
            'work_experience' => '3 years experience'
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['job_post_id']);
    }

    public function test_store_with_inactive_job_post()
    {
        $jobPost = JobPost::factory()->create(['is_active' => false]);

        $applicationData = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Jane Smith',
            'phone_number' => '+1234567890',
            'work_experience' => '3 years experience'
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'This job post is no longer accepting applications'
            ]);
    }

    public function test_store_with_expired_job_post()
    {
        $jobPost = JobPost::factory()->create([
            'is_active' => true,
            'application_deadline' => Carbon::now()->subDays(1)
        ]);

        $applicationData = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Jane Smith',
            'phone_number' => '+1234567890',
            'work_experience' => '3 years experience'
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'The application deadline for this job has passed'
            ]);
    }

    public function test_store_with_invalid_phone_number()
    {
        $jobPost = JobPost::factory()->create(['is_active' => true]);

        $applicationData = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Jane Smith',
            'phone_number' => 'invalid-phone',
            'work_experience' => '3 years experience'
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone_number']);
    }

    public function test_store_with_invalid_email()
    {
        $jobPost = JobPost::factory()->create(['is_active' => true]);

        $applicationData = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Jane Smith',
            'phone_number' => '+1234567890',
            'email' => 'invalid-email',
            'work_experience' => '3 years experience'
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_with_too_long_work_experience()
    {
        $jobPost = JobPost::factory()->create(['is_active' => true]);

        $applicationData = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Jane Smith',
            'phone_number' => '+1234567890',
            'work_experience' => str_repeat('a', 1001) // Over 1000 characters
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['work_experience']);
    }

    public function test_store_without_optional_email()
    {
        $jobPost = JobPost::factory()->create(['is_active' => true]);

        $applicationData = [
            'job_post_id' => $jobPost->id,
            'full_name' => 'Jane Smith',
            'phone_number' => '+1234567890',
            'work_experience' => '3 years experience'
            // email is optional
        ];

        $response = $this->postJson('/api/job-applications', $applicationData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => null
                ]
            ]);
    }
}
