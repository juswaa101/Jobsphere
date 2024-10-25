@extends('layouts.auth')

@section('title', 'Jobsphere - Submitted Applications')

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

        .company-logo {
            max-width: 50px;
            height: auto;
            margin-right: 15px;
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
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="dashboard-header">
            <h1>Submitted Applications</h1>
            <p>Review applications submitted by job seekers.</p>
        </div>

        <div class="dashboard-content">
            <div class="filter-form row">
                <form method="GET" action="{{ route('job.own') }}" class="col-md-12">
                    <div class="row">
                        <label class="text-muted fw-bold">Filter Applications By Status:</label>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Viewed</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Interviewed</option>
                                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Hired</option>
                                <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Not Moving Forward
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                @if ($applications->count())
                    <h1 class="fw-bold mt-2">Applications</h1>
                    @foreach ($applications as $application)
                        <div class="col-md-4">
                            <div class="job-posting">
                                <div class="job-header">
                                    <div class="d-flex align-items-center">
                                        @if ($application->company_logo)
                                            <img src="{{ asset('storage/' . $application->company_logo) }}"
                                                alt="{{ $application->company }}" class="company-logo">
                                        @endif
                                        <div>
                                            <h5 class="fw-bold m-0">{{ $application->title }}</h5>
                                            <h6 class="fst-italic m-0">{{ $application->company }}</h6>
                                            <h6 class="fst-italic m-0">
                                                <span
                                                    class="badge mt-2
                                                    @if ($application->pivot->job_status == 0) bg-warning
                                                    @elseif($application->pivot->job_status == 1) bg-info
                                                    @elseif($application->pivot->job_status == 2) bg-primary
                                                    @elseif($application->pivot->job_status == 3) bg-success
                                                    @elseif($application->pivot->job_status == 4) bg-danger @endif">
                                                    {{ ['Pending', 'Viewed', 'Interviewed', 'Hired', 'Not Moving Forward'][$application->pivot->job_status] }}
                                                </span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="job-description" id="description-{{ $application->id }}">
                                    <p>Cover Letter: {{ $application->pivot->cover_letter }}</p>
                                </div>
                                <span class="view-more" id="view-more-{{ $application->id }}">View More...</span>
                                <a href="{{ route('application.details', ['job' => $application->id]) }}"
                                    class="btn btn-secondary">View Details</a>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination if needed -->
                    {{ $applications->links('pagination::bootstrap-5') }}
                @else
                    <p class="text-center fw-bold mt-3">No applications found.</p>
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

            // Show "View More" if the content is overflowing
            if (item.scrollHeight > 100) {
                viewMore.style.display = 'block';
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
