<?php

namespace App\Jobs;

use App\Mail\SendJobApplicationRequestMail;
use App\Models\Job;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Log;

class SendJobApplicationRequestJobs implements ShouldQueue, ShouldHandleEventsAfterCommit
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
            // Send the Job application request from both employer and job seeker
            Mail::to($this->jobInfo?->employer?->email)
                ->bcc($this->userInfo?->email)
                ->send(
                    new SendJobApplicationRequestMail($this->jobInfo, $this->userInfo)
                );

            Log::info('Send Job Application Request Success!');
        } catch (\Exception $e) {
            Log::error('Send Job Application Request Fails to Send!');
        }
    }
}
