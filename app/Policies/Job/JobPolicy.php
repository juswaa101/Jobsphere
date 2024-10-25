<?php

namespace App\Policies\Job;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    /**
     * Authorized employer to post job
     *
     * @return bool
     */
    public function accessJobPosting(User $user)
    {
        return $user->employer();
    }

    /**
     * Authorized employer to review job application
     *
     * @return bool
     */
    public function accessJobApplication(User $user)
    {
        return $user->employer();
    }

    /**
     * Authorized employer to update job application status
     *
     * @return bool
     */
    public function accessJobUpdateStatus(User $user)
    {
        return $user->employer();
    }
}
