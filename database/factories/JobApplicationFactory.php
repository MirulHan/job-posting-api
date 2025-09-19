<?php

namespace Database\Factories;

use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_post_id' => JobPost::factory(),
            'full_name' => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->optional(0.8)->safeEmail(), // 80% chance of having email
            'work_experience' => $this->generateWorkExperience(),
            'status' => $this->faker->randomElement(['applied', 'screening', 'interview', 'offer', 'accepted', 'failed']),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Generate realistic work experience text.
     */
    private function generateWorkExperience(): string
    {
        $experiences = [
            '2 years of experience in web development using PHP and Laravel framework',
            '5+ years of software development experience with strong background in JavaScript and React',
            'Fresh graduate with internship experience in full-stack development',
            '3 years of experience in backend development, proficient in PHP, Python, and database management',
            '7 years of experience in software engineering with expertise in cloud technologies and microservices',
            '1 year of experience in frontend development using modern JavaScript frameworks',
            '4 years of experience in mobile app development for iOS and Android platforms',
            '6 years of experience in DevOps and system administration with AWS and Docker',
            'Entry-level developer with strong foundation in computer science and passion for learning',
            '8+ years of experience in enterprise software development and team leadership',
        ];

        return $this->faker->randomElement($experiences);
    }

    /**
     * Indicate that the application status is applied.
     */
    public function applied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'applied',
        ]);
    }

    /**
     * Indicate that the application status is screening.
     */
    public function screening(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'screening',
        ]);
    }

    /**
     * Indicate that the application status is interview.
     */
    public function interview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'interview',
        ]);
    }

    /**
     * Indicate that the application status is offer.
     */
    public function offer(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'offer',
        ]);
    }

    /**
     * Indicate that the application status is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    /**
     * Indicate that the application status is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Indicate that the application is for a specific job post.
     */
    public function forJobPost(JobPost $jobPost): static
    {
        return $this->state(fn (array $attributes) => [
            'job_post_id' => $jobPost->id,
        ]);
    }

    /**
     * Indicate that the application has no email.
     */
    public function withoutEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
        ]);
    }

    /**
     * Indicate that the application has a specific full name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'full_name' => $name,
        ]);
    }
}
