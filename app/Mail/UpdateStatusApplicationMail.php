<?php

namespace App\Mail;

use App\Models\Job;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpdateStatusApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    private Job $jobInfo;
    private User $userInfo;
    private $application;

    /**
     * Create a new message instance.
     */
    public function __construct(Job $jobInfo, User $userInfo)
    {
        $this->jobInfo = $jobInfo;
        $this->userInfo = $userInfo;
        $this->application = $this->userInfo
            ->jobs()
            ->where('job_id', $this->jobInfo->id)
            ->where('applicant_id', $this->userInfo->id)
            ->firstOrFail();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Update for ' . $this->jobInfo?->title . ' at ' . $this->jobInfo?->company,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'vendor.mail.update-status-application',
            with: [
                'application' => $this->application,
                'userInfo' => $this->userInfo,
                'jobInfo' => $this->jobInfo,
                'subject' => 'Application Update for ' . $this->jobInfo?->title . ' at ' . $this->jobInfo?->company,
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
        // Return the email with attached resume of the applicant
        return [
            Attachment::fromStorageDisk('public', $this->application?->pivot?->resume_cv)
        ];
    }
}
