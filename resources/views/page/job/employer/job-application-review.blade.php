@extends('layouts.auth')

@section('title', 'Jobsphere - Job Application Review')

@section('styles')
    <!-- DataTables and DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">
@endsection

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="fw-bold mb-4">Review Job Applications</h1>

            <div>
                <button id="reloadTable" class="btn mb-3">
                    <i class="fas fa-sync-alt"></i> Reload
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="applicantTable" class="table table-bordered table-hovered w-100">
                <thead>
                    <tr>
                        <th>Company Logo</th>
                        <th>Company</th>
                        <th>Job Title</th>
                        <th>Applicant Name</th>
                        <th>Email</th>
                        <th>Resume</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('page.job.employer.modal.view-resume') <!-- Modal to view resumes -->
@endsection

@section('scripts')
    <!-- DataTables and DataTables Responsive JS -->
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            let applicantTable = $('#applicantTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('job.application-listings') }}', // Update with your route for applicants
                columns: [{
                        data: 'logo', // Adjust based on the response structure
                        name: 'logo'
                    },
                    {
                        data: 'company', // Adjust based on the response structure
                        name: 'company'
                    },
                    {
                        data: 'job_title', // Adjust based on the response structure
                        name: 'job_title'
                    },
                    {
                        data: 'applicant_name', // Adjust based on the response structure
                        name: 'name'
                    },
                    {
                        data: 'applicant_email',
                        name: 'email'
                    },
                    {
                        data: 'resume', // Assume this contains a link to the resume
                        name: 'resume',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'applied_on',
                        name: 'applied_on'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Event listener for reload button
            $('#reloadTable').on('click', function() {
                // Reload the table without resetting the pagination
                applicantTable.ajax.reload(null, false);
            });

            // Handle view resume button click
            $('#applicantTable').on('click', '.view-resume', function() {
                const userId = $(this).data('user-id'); // User ID
                const jobId = $(this).data('job-id'); // Job ID
                const resumeLink = $(this).data('resume-link');

                // Populate the modal
                $.ajax({
                    url: `{{ route('job.get.application', ['user' => ':user', 'job' => ':job']) }}`
                        .replace(':user', userId)
                        .replace(':job', jobId),
                    type: 'GET',
                    success: function(response) {
                        const applicant = response.data;

                        // Populate modal fields
                        $('#applicantName').text(applicant.name);
                        $('#applicantEmailDisplay').text(applicant.email);
                        $('#jobTitle').text(applicant.job_title);
                        $('#companyTitle').text(applicant.company);

                        const resumeUrl = applicant.resume_cv;

                        // Clear previous content
                        $('#resumeContent').attr('src', ''); // Clear the iframe
                        $('#applicantResumeLink').attr('href',
                            resumeUrl); // Set link for download
                        $('#applicantResumeLink').text('Download Resume'); // Update link text

                        // Check file extension to determine how to display
                        if (resumeUrl) {
                            const fileExtension = resumeUrl.split('.').pop().toLowerCase();

                            if (fileExtension === 'pdf') {
                                // Show PDF in iframe using Google Docs Viewer
                                $('#resumeContent').attr('src',
                                    `https://docs.google.com/gview?url=${resumeUrl}&embedded=true`
                                );
                                $('#resumeContent').show(); // Show iframe
                            } else if (fileExtension === 'doc' || fileExtension === 'docx') {
                                // Hide iframe and show message for unsupported Word files
                                $('#resumeContent').hide(); // Hide iframe
                                $('#resumeMessage').text(
                                    'This file type is not supported for viewing in the browser. Please download it.'
                                ).show();
                            } else {
                                // For any other unsupported formats
                                $('#resumeContent').hide(); // Hide iframe
                                $('#resumeMessage').text(
                                    'This file type is not supported for viewing in the browser. Please download it.'
                                ).show();
                            }
                        } else {
                            // Hide iframe and download link if no resume exists
                            $('#resumeContent').hide();
                            $('#applicantResumeLink').hide();
                        }

                        // Show the modal
                        $('#viewResumeModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to retrieve applicant details. Please try again.',
                        });
                    }
                });
            });

            // Handle application update status
            $('#applicantTable').on('change', '.change-status', function() {
                const userId = $(this).data('user-id'); // User ID
                const jobId = $(this).data('job-id'); // Job ID
                let application_status = $(this).val();

                // Call request to update application status
                $.ajax({
                    url: `{{ route('update.application', ['user' => ':user', 'job' => ':job']) }}`
                        .replace(':user', userId)
                        .replace(':job', jobId),
                    type: 'POST',
                    data: {
                        _method: 'PATCH',
                        application_status
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Application status updated successfully!',
                        });

                        // Reload the table without resetting the pagination
                        applicantTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update application status. Please try again.',
                        });
                    }
                });
            });

            // Handle delete job application
            $('#applicantTable').on('click', '.delete-application', function() {
                const userId = $(this).data('user-id'); // User ID
                const jobId = $(this).data('job-id'); // Job ID

                // SweetAlert2 confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this job application!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Call request to delete application
                        $.ajax({
                            url: `{{ route('delete.application', ['user' => ':user', 'job' => ':job']) }}`
                                .replace(':user', userId)
                                .replace(':job', jobId),
                            type: 'DELETE',
                            dataType: 'json',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Job application deleted successfully!',
                                });

                                // Reload the table without resetting the pagination
                                applicantTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to delete job application. Please try again.',
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
