<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_post_id',
        'full_name',
        'phone_number',
        'email',
        'work_experience',
        'status',
    ];

    /**
     * Get the job post that this application is for.
     */
    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }
}
