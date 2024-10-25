@component('mail::message')
# {{ $subject }}

Dear {{ $jobInfo->company }},

{{ $userInfo->name }} has submitted an application for the following job position:

@component('mail::panel')
### {{ $jobInfo->title }}
- **Company:** {{ $jobInfo->company }}
- **Salary:** Php {{ $jobInfo->salary_from }} - {{ $jobInfo->salary_to }}
- **Job Description:** {{ $jobInfo->description }}
- **Application Submitted By:** {{ $userInfo->name }}
- **Email:** {{ $userInfo->email }}
@endcomponent

Thank you for considering this application. We look forward to your response.

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent
