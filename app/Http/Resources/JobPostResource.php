<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'company' => $this->company,
            'location' => $this->location,
            'job_type' => $this->job_type,
            'salary' => $this->when($this->salary, $this->salary),
            'contact_email' => $this->contact_email,
            'skills' => $this->when($this->skills, $this->skills),
            'application_deadline' => $this->when(
                $this->application_deadline,
                $this->application_deadline ? $this->application_deadline->format('Y-m-d') : null
            ),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
