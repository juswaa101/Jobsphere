<?php

namespace App\Jobs;

use App\Mail\UpdateStatusApplicationMail;
use App\Models\Job;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Log;

class UpdateStatusApplicationJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Job $jobInfo;
    private User $userInfo;

    /**
     * Create a new job instance.
     */
    public function __construct(Job $jobInfo, User $userInfo)
    {
        $this->jobInfo = $jobInfo;
        $this->userInfo = $userInfo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load the employer relationship
        $this->jobInfo->load('employer');

        try {
            // Send the update status of the application to the applicant
            Mail::to($this->userInfo?->email)
                ->send(
                    new UpdateStatusApplicationMail($this->jobInfo, $this->userInfo)
                );

            Log::info('Update Status Application Success!');
        } catch (\Exception $e) {
            Log::error('Update Status Application Fails to Send!');
        }
    }
}
