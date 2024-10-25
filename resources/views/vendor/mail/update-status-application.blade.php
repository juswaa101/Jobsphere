@component('mail::message')
# Application Status Update

Hello {{ $userInfo->name }},

We wanted to update you regarding your application for the position of **{{ $jobInfo->title }}** at **{{ $jobInfo->company }}**.

@php
    // Application status
    $statusText = \App\Models\Job::$statuses[$application?->pivot?->job_status] ?? 'Unknown Status';
@endphp

Your current application status is: **{{ $statusText }}**

### Job Details:
- **Job Title:** {{ $jobInfo->title }}
- **Company:** {{ $jobInfo->company }}
- **Salary:**
@if($jobInfo->salary_from && $jobInfo->salary_to)
₱{{ number_format($jobInfo->salary_from, 2) }} - ₱{{ number_format($jobInfo->salary_to, 2) }}
@else
Not specified
@endif
- **Posted By:** {{ $jobInfo?->employer?->name }}
- **Status:** {{ $statusText }}

We appreciate your interest in the position and we will continue to keep you updated as your application progresses.

@component('mail::button', ['url' => route('application.details', ['job' => $jobInfo->id])])
View Application
@endcomponent

Thank you for using our platform!

Best Regards,
The {{ config('app.name') }} Team
@endcomponent
