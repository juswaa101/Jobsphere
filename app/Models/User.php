<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $user_role = [
        1 => 'JOBSEEKER',
        2 => 'EMPLOYER'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed'
    ];

    /**
     * Check if user is job seeker
     *
     * @return bool
     */
    public function jobseeker()
    {
        return $this->role == 1 && auth()->check();
    }

    /**
     * Check if user is employer
     *
     * @return bool
     */
    public function employer()
    {
        return $this->role == 2 && auth()->check();
    }

    /**
     * Get all of the job_postings for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function job_postings()
    {
        return $this->hasMany(Job::class, 'id', 'user_created_by');
    }

    /**
     * The jobs that belong to the applicants
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'job_user', 'applicant_id', 'job_id')
            ->withPivot('job_id', 'applicant_id', 'cover_letter', 'resume_cv', 'job_status')
            ->withTimestamps();
    }
}
