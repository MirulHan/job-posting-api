<?php

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Database\Seeder;

class JobApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure we have job posts
        if (JobPost::count() == 0) {
            $this->call(JobPostSeeder::class);
        }

        // Get all job post IDs
        $jobPostIds = JobPost::pluck('id')->toArray();

        $applications = [
            [
                'full_name' => 'John Smith',
                'phone_number' => '555-123-4567',
                'email' => 'john.smith@example.com',
                'work_experience' => '5 years of experience as a Frontend Developer working with React, JavaScript, and modern CSS frameworks. Led a team of 3 developers on a major e-commerce site redesign that increased conversions by 25%.',
                'status' => 'interview',
            ],
            [
                'full_name' => 'Sarah Johnson',
                'phone_number' => '555-987-6543',
                'email' => 'sarah.j@example.com',
                'work_experience' => '3 years of experience in UI/UX design, proficient in Figma and Adobe XD. Created user interfaces for mobile applications with over 1 million downloads.',
                'status' => 'screening',
            ],
            [
                'full_name' => 'Mike Williams',
                'phone_number' => '555-456-7890',
                'email' => 'mikew@example.com',
                'work_experience' => '7 years of backend development experience with PHP and Laravel. Designed and implemented RESTful APIs for enterprise clients. Familiar with AWS infrastructure.',
                'status' => 'applied',
            ],
            [
                'full_name' => 'Emily Chen',
                'phone_number' => '555-222-3333',
                'email' => 'emily.chen@example.com',
                'work_experience' => '4 years as a Full Stack Developer working with MERN stack. Developed a customer portal that reduced support tickets by 30%. Experienced in Agile development methodologies.',
                'status' => 'offer',
            ],
            [
                'full_name' => 'David Rodriguez',
                'phone_number' => '555-888-9999',
                'email' => null, // No email provided
                'work_experience' => '2 years of experience in web development. Proficient in HTML, CSS, and basic JavaScript. Familiar with WordPress and content management systems.',
                'status' => 'applied',
            ],
            [
                'full_name' => 'Amanda Taylor',
                'phone_number' => '555-333-4444',
                'email' => 'amanda.t@example.com',
                'work_experience' => '6 years working as a DevOps engineer. Expert in CI/CD pipelines, Docker, and Kubernetes. Implemented automated deployment processes that reduced deployment time by 70%.',
                'status' => 'accepted',
            ],
            [
                'full_name' => 'James Wilson',
                'phone_number' => '555-777-8888',
                'email' => 'james.w@example.com',
                'work_experience' => '3 years of experience as a JavaScript developer. Proficient in React and Node.js. Developed several e-commerce websites and payment integrations.',
                'status' => 'failed',
            ],
        ];

        foreach ($applications as $application) {
            // Assign a random job post ID
            $application['job_post_id'] = $jobPostIds[array_rand($jobPostIds)];

            JobApplication::create($application);
        }
    }
}
