<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
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
            'job_post_id' => $this->job_post_id,
            'job_title' => $this->whenLoaded('jobPost', function () {
                return $this->jobPost->title;
            }),
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'email' => $this->when($this->email, $this->email),
            'work_experience' => $this->work_experience,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
