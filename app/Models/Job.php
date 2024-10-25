<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    const STATUS_PENDING = 0;
    const STATUS_VIEWED = 1;
    const STATUS_INTERVIEWED = 2;
    const STATUS_HIRED = 3;
    const STATUS_NOT_MOVING_FORWARD = 4;

    // Array of statuses for easy reference
    public static $statuses = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_VIEWED => 'Viewed',
        self::STATUS_INTERVIEWED => 'Interviewed',
        self::STATUS_HIRED => 'Hired',
        self::STATUS_NOT_MOVING_FORWARD => 'Not Moving Forward',
    ];

    protected $fillable = [
        'title',
        'description',
        'company',
        'salary_from',
        'salary_to',
        'company_logo',
        'is_active',
        'expiry_date',
        'user_created_by'
    ];

    /**
     * Get the employer that owns the Job
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employer()
    {
        return $this->belongsTo(User::class, 'user_created_by', 'id');
    }

    /**
     * The applicants that belong to the Job
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function applicants()
    {
        return $this->belongsToMany(User::class, 'job_user', 'job_id', 'applicant_id')
            ->withPivot('job_id', 'applicant_id', 'cover_letter', 'resume_cv', 'job_status')
            ->withTimestamps();
    }
}
