@extends('layouts.auth')

@section('title', 'Jobsphere - Application Details')

@section('styles')
    <style>
        .dashboard-header {
            background-color: #2C3E50;
            color: white;
            padding: 40px 0;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 0;
        }

        .dashboard-content {
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .job-posting {
            padding: 20px;
            margin: 20px 0;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .company-logo {
            max-width: 50px;
            height: auto;
            margin-right: 15px;
        }

        .section-title {
            margin-top: 20px;
            font-weight: bold;
            font-size: 1.2rem;
            color: #2C3E50;
        }

        .detail-item {
            margin: 10px 0;
        }

        .resume-link {
            margin-top: 20px;
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        .resume-link:hover {
            background-color: #0056b3;
        }

        /* .btn-back {
            margin-top: 20px;
        } */

        .badge-status {
            margin-top: 5px;
            font-size: 0.9rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="dashboard-header">
            <h1>Application Details</h1>
        </div>

        <div class="dashboard-content">
            <div class="job-posting">
                <div class="d-flex align-items-center mb-3">
                    @if ($application->company_logo)
                        <img src="{{ asset('storage/' . $application->company_logo) }}" alt="{{ $application->company }}"
                            class="company-logo">
                    @endif
                    <div>
                        <h5 class="fw-bold m-0">{{ $application->title }}</h5>
                        <h6 class="fst-italic m-0">{{ $application->company }}</h6>
                        <h6 class="fst-italic m-0 badge-status">
                            <span
                                class="badge
                                @if ($application->pivot->job_status == 0) bg-warning
                                @elseif ($application->pivot->job_status == 1) bg-info
                                @elseif ($application->pivot->job_status == 2) bg-primary
                                @elseif ($application->pivot->job_status == 3) bg-success
                                @elseif ($application->pivot->job_status == 4) bg-danger @endif">
                                {{ ['Pending', 'Viewed', 'Interviewed', 'Hired', 'Not Moving Forward'][$application->pivot->job_status] }}
                            </span>
                        </h6>
                    </div>
                </div>

                <h6 class="section-title">Applicant Information:</h6>
                <div class="detail-item"><strong>Name:</strong> {{ auth()->user()->name }}</div>
                <div class="detail-item"><strong>Email:</strong> {{ auth()->user()->email }}</div>

                <h6 class="section-title">Cover Letter:</h6>
                <p>{{ $application->pivot->cover_letter }}</p>

                @if ($application->pivot->resume_cv)
                    <h6 class="section-title">Resume:</h6>
                    <a href="{{ asset('storage/' . $application->pivot->resume_cv) }}" class="resume-link"
                        target="_blank">View Resume</a>
                @endif
                <a href="{{ route('job.own') }}" class="btn btn-back">Back to Applications</a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
