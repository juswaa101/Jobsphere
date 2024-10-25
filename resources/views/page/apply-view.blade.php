@extends('layouts.auth')

@section('title', 'Apply for ' . $job->title)

@section('styles')
    <style>
        .apply-header {
            background-color: #2C3E50;
            color: white;
            padding: 40px 0;
            text-align: center;
            margin: 0;
        }

        .apply-content {
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .job-details {
            margin-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .job-logo {
            max-width: 100px;
            margin-right: 20px;
        }

        .job-title {
            font-weight: bold;
            font-size: 1.8rem;
        }

        .job-company {
            font-style: italic;
            font-size: 1.3rem;
        }

        .salary-text {
            color: #868686;
            font-size: 16px;
        }

        .application-form {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .application-form input,
        .application-form textarea {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 100%;
            transition: border-color 0.3s;
        }

        .application-form input:focus,
        .application-form textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
        }

        .application-form button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 48%;
            /* Adjust width for side by side */
            transition: background-color 0.3s;
        }

        .application-form button.cancel-button {
            background-color: #dc3545;
        }

        .application-form button.cancel-button:hover {
            background-color: #c82333;
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="apply-header">
            <h1>Apply for {{ $job->title }}</h1>
        </div>

        <div class="apply-content">
            <div class="job-details">
                @if ($job->company_logo)
                    <img src="{{ asset('storage/' . $job->company_logo) }}" alt="{{ $job->company }} Logo" class="job-logo">
                @endif
                <div>
                    <h2 class="job-title">{{ $job->title }}</h2>
                    <h3 class="job-company">{{ $job->company }}</h3>
                    <h4 class="salary-text">Salary: Php {{ $job->salary_from }} - {{ $job->salary_to }}</h4>
                    <p>{{ $job->description }}</p>
                    <span class="expiry-badge">
                        Expires in {{ \Carbon\Carbon::parse($job->expiry_date)->diffInDays(now()) }} days
                    </span>
                </div>
            </div>

            <h3>Application Form</h3>
            @if (!$alreadyApplied)
                <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                    You have not yet applied for this job. Don't miss your chance!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @else
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    You have already applied to this job!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('job.apply.submit', ['job' => $job->id]) }}" method="POST" class="application-form"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3 form-group">
                        <label class="form-label">Name: </label>
                        <input type="text" name="name" placeholder="Your Name"
                            value="{{ auth()->user()->name ?? '' }}" required readonly>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3 form-group">
                        <label class="form-label">Email: </label>
                        <input type="email" name="email" placeholder="Your Email"
                            value="{{ auth()->user()->email ?? '' }}" required readonly>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-3 form-group">
                        <label class="form-label">Cover Letter (Optional): </label>
                        <textarea name="cover_letter" placeholder="Cover Letter" rows="5">{{ old('cover_letter') }}</textarea>
                        @error('cover_letter')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-3 form-group">
                        <label class="form-label">Resume/CV: </label>
                        <input type="file" name="resume" accept=".pdf,.doc,.docx">
                        @error('resume')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="button-group">
                    @if (!$alreadyApplied)
                        <button type="submit">Submit Application</button>
                    @endif
                    <a href="{{ route('dashboard.home') }}" class="btn cancel-button">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
