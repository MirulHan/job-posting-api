<?php

namespace Database\Factories;

use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPost>
 */
class JobPostFactory extends Factory
{
    protected $model = JobPost::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraphs(3, true),
            'company' => $this->faker->company(),
            'location' => $this->faker->city() . ', ' . $this->faker->stateAbbr(),
            'job_type' => $this->faker->randomElement(['full_time', 'part_time', 'contract', 'internship']),
            'salary' => $this->faker->numberBetween(50000, 200000),
            'contact_email' => $this->faker->companyEmail(),
            'skills' => $this->faker->randomElements([
                'PHP', 'Laravel', 'JavaScript', 'React', 'Vue.js', 'MySQL', 'PostgreSQL',
                'Docker', 'AWS', 'Git', 'HTML', 'CSS', 'Python', 'Node.js', 'TypeScript'
            ], $this->faker->numberBetween(2, 6)),
            'application_deadline' => $this->faker->optional(0.7)->dateTimeBetween('+1 week', '+3 months'),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the job post is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the job post is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the job post has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'application_deadline' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }

    /**
     * Indicate that the job post is for a specific company.
     */
    public function forCompany(string $companyName): static
    {
        return $this->state(fn (array $attributes) => [
            'company' => $companyName,
        ]);
    }

    /**
     * Indicate that the job post is for a specific job type.
     */
    public function ofType(string $jobType): static
    {
        return $this->state(fn (array $attributes) => [
            'job_type' => $jobType,
        ]);
    }
}
