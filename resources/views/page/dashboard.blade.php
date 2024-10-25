@extends('layouts.auth')

@section('title', 'Jobsphere - Dashboard')

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

        .filter-form {
            margin: 20px 0;
            padding: 15px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-form input,
        .filter-form select {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 100%;
        }

        .filter-form button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        .job-posting {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Space items evenly */
            padding: 20px;
            margin: 20px 0;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.2s;
        }

        .job-posting:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .job-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .company-logo {
            max-width: 50px;
            height: auto;
            margin-right: 15px;
        }

        .expiry-badge {
            background-color: #ffc107;
            color: #212529;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 500;
            line-height: 1;
        }

        .apply-button {
            margin-top: 10px;
            background-color: #28a745;
            color: white;
            border-radius: 4px;
            padding: 10px 15px;
            text-decoration: none;
            display: inline-block;
        }

        .apply-button:hover {
            background-color: #218838;
        }

        .job-description {
            max-height: 100px;
            /* Set maximum height for description */
            overflow: hidden;
            /* Hide overflow */
            text-overflow: ellipsis;
            /* Add ellipsis */
            position: relative;
            /* For absolute positioning */
        }

        .view-more {
            display: none;
            /* Initially hidden */
            cursor: pointer;
            /* Pointer cursor for links */
            color: #007bff;
            /* Link color */
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 1.8rem;
            }

            .dashboard-header p {
                font-size: 1rem;
            }

            .filter-form {
                display: flex;
                flex-direction: column;
            }
        }

        .salary-text {
            color: #868686 !important;
            font-size: 14px !important;
        }

        /* Add this CSS to your existing styles */
        .no-job-postings {
            background-color: #eafaf1;
            /* Soft green background */
            color: #4b8a3d;
            /* Dark green text */
            border-radius: 10px;
            /* Rounded corners */
            padding: 30px;
            /* Increased padding for spaciousness */
            text-align: center;
            /* Center the text */
            margin-top: 20px;
            /* Space from the elements above */
            font-weight: bold;
            /* Bold text */
            font-size: 1.5rem;
            /* Larger font size */
            position: relative;
            /* For positioning the icon */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            /* Soft shadow */
        }

        .no-job-postings i {
            font-size: 4rem;
            /* Larger icon */
            position: absolute;
            /* Absolute positioning for the icon */
            top: -20px;
            /* Position above the message */
            left: 50%;
            /* Center horizontally */
            transform: translateX(-50%);
            /* Centering adjustment */
            color: #4b8a3d;
            /* Match text color */
        }

        .no-job-postings p {
            font-size: 1.2rem;
            /* Slightly larger paragraph text */
            margin-top: 10px;
            /* Space above the paragraph */
            color: #6c757d;
            /* Neutral text color */
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="dashboard-header">
            <h1>Your Job Application Hub</h1>
            <p>Track your applications and discover exciting job opportunities.</p>
        </div>

        <div class="dashboard-content">
            <div class="row">
                @include('components.alerts.alert')
            </div>
            <div class="filter-form row">
                <form method="GET" action="{{ route('dashboard.home') }}" class="col-md-12">
                    <div class="row">
                        <label class="text-muted fw-bold">Filter Job Posting By:</label>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <input type="text" name="title" placeholder="Job Title" value="{{ request('title') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="company" placeholder="Company" value="{{ request('company') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="salary_from" placeholder="Salary From"
                                value="{{ request('salary_from') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="salary_to" placeholder="Salary To"
                                value="{{ request('salary_to') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                @if ($jobPostings->count())
                    <h1 class="fw-bold mt-2">Jobs Available</h1>
                    @foreach ($jobPostings as $job)
                        <div class="col-md-4">
                            <div class="job-posting">
                                <div class="job-header">
                                    <div class="d-flex align-items-center">
                                        @if ($job->company_logo)
                                            <img src="{{ asset('storage/' . $job->company_logo) }}"
                                                alt="{{ $job->company }}" class="company-logo">
                                        @endif
                                        <div>
                                            <h5 class="fw-bold m-0">{{ $job->title }}</h5>
                                            <h6 class="fst-italic m-0">{{ $job->company }}</h6>
                                            <h6 class="fst-italic salary-text m-0">Php {{ $job->salary_from }} -
                                                {{ $job->salary_to }}</h6>
                                        </div>
                                    </div>
                                    <span
                                        class="expiry-badge">{{ \Carbon\Carbon::parse($job->expiry_date)->diffInDays(now()) }}
                                        days left</span>
                                </div>
                                <div class="job-description" id="description-{{ $job->id }}">
                                    <p>{{ $job->description }}</p>
                                </div>
                                <span class="view-more" id="view-more-{{ $job->id }}">View More...</span>
                                <a href="{{ route('job.apply', $job->id) }}" class="btn apply-button">Apply</a>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    {{ $jobPostings->links('pagination::bootstrap-5') }}
                @else
                    <div class="col-md-12">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-frown"></i> <!-- Example icon -->
                            &nbsp; No job postings found. Check back later or create a new job posting!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('.job-description').forEach(item => {
            const jobId = item.id.split('-')[1];
            const viewMore = document.getElementById(`view-more-${jobId}`);

            if (item.scrollHeight > 100) {
                viewMore.style.display = 'block'; // Show "View More" if the content is overflowing
            }

            viewMore.addEventListener('click', function() {
                if (item.style.maxHeight) {
                    item.style.maxHeight = null; // Collapse the description
                    viewMore.textContent = 'View More...'; // Reset the text
                } else {
                    item.style.maxHeight = item.scrollHeight + "px"; // Expand the description
                    viewMore.textContent = 'Show Less'; // Change the text
                }
            });
        });
    </script>
@endsection
