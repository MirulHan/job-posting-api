<?php

namespace Database\Seeders;

use App\Models\JobPost;
use Illuminate\Database\Seeder;

class JobPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobPosts = [
            [
                'title' => 'Frontend Developer',
                'description' => 'We are looking for an experienced Frontend Developer to join our team. The successful candidate will be responsible for implementing visual elements that users see and interact with in a web application.',
                'company' => 'Tech Solutions Inc.',
                'location' => 'New York, NY',
                'job_type' => 'Full-time',
                'salary' => 85000.00,
                'contact_email' => 'careers@techsolutions.com',
                'skills' => ['JavaScript', 'React', 'HTML', 'CSS', 'TypeScript'],
                'application_deadline' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'title' => 'Backend Engineer',
                'description' => 'We are seeking a talented Backend Engineer to develop and maintain server-side applications. You will be responsible for the server-side logic, database interactions, and API integrations.',
                'company' => 'Data Systems LLC',
                'location' => 'San Francisco, CA',
                'job_type' => 'Full-time',
                'salary' => 95000.00,
                'contact_email' => 'jobs@datasystems.com',
                'skills' => ['PHP', 'Laravel', 'MySQL', 'API Development', 'AWS'],
                'application_deadline' => now()->addDays(45),
                'is_active' => true,
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => 'We are looking for a creative UI/UX Designer to design intuitive and engaging user interfaces for our web and mobile applications.',
                'company' => 'Creative Agency Co.',
                'location' => 'Remote',
                'job_type' => 'Contract',
                'salary' => 75000.00,
                'contact_email' => 'hr@creativeagency.com',
                'skills' => ['Figma', 'Adobe XD', 'Sketch', 'User Research', 'Prototyping'],
                'application_deadline' => now()->addDays(15),
                'is_active' => true,
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Join our team as a DevOps Engineer to help us build and maintain infrastructure, automate processes, and ensure smooth deployments.',
                'company' => 'CloudOps Technologies',
                'location' => 'Austin, TX',
                'job_type' => 'Full-time',
                'salary' => 110000.00,
                'contact_email' => 'careers@cloudops.com',
                'skills' => ['Docker', 'Kubernetes', 'AWS', 'CI/CD', 'Terraform'],
                'application_deadline' => now()->addDays(60),
                'is_active' => true,
            ],
            [
                'title' => 'Full Stack Developer',
                'description' => 'We are seeking a Full Stack Developer to work on both front-end and back-end development of our web applications.',
                'company' => 'WebApps Inc.',
                'location' => 'Chicago, IL',
                'job_type' => 'Full-time',
                'salary' => 90000.00,
                'contact_email' => 'jobs@webappsinc.com',
                'skills' => ['JavaScript', 'React', 'Node.js', 'MongoDB', 'Express'],
                'application_deadline' => now()->addDays(30),
                'is_active' => true,
            ],
        ];

        foreach ($jobPosts as $jobPost) {
            JobPost::create($jobPost);
        }
    }
}
