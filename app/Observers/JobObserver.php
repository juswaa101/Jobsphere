<?php

namespace App\Observers;

use App\Models\Job;
use App\Models\User;
use App\Jobs\SendJobApplicationRequestJobs;
use App\Jobs\UpdateStatusApplicationJobs;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class JobObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Event for attaching a job request
     *
     * @param User $user
     * @param Job $job
     * @return void
     */
    public function attached(User $user, Job $job)
    {
        // Send the application request both job seeker and employer
        dispatch(new SendJobApplicationRequestJobs($job, $user));
    }

    /**
     * Event for updating a status of job request
     *
     * @param User $user
     * @param Job $job
     * @return void
     */
    public function updateStatus(User $user, Job $job)
    {
        // Send the application update status for the applicant
        dispatch(new UpdateStatusApplicationJobs($job, $user));
    }
}
