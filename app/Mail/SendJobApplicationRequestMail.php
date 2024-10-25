<?php

namespace App\Mail;

use App\Models\Job;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendJobApplicationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    private Job $jobInfo;
    private User $userInfo;

    /**
     * Create a new message instance.
     */
    public function __construct(Job $jobInfo, User $userInfo)
    {
        $this->jobInfo = $jobInfo;
        $this->userInfo = $userInfo;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Job Application Request for ' . $this->jobInfo?->title . ' at ' . $this->jobInfo?->company,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'vendor.mail.job-app-request',
            with: [
                'userInfo' => $this->userInfo,
                'jobInfo' => $this->jobInfo,
                'subject' => 'Job Application Request for ' . $this->jobInfo?->title . ' at ' . $this->jobInfo?->company,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Get the applicants request
        $application = $this->userInfo
            ->jobs()
            ->where('job_id', $this->jobInfo->id)
            ->where('applicant_id', $this->userInfo->id)
            ->firstOrFail();

        // Return the email with attached resume of the applicant
        return [
            Attachment::fromStorageDisk('public', $application?->pivot?->resume_cv)
        ];
    }
}
