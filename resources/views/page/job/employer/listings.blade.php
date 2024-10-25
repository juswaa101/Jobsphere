@extends('layouts.auth')

@section('title', 'Jobsphere - Job Post Listings')

@section('styles')
    <!-- DataTables and DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">
@endsection

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="fw-bold mb-4">Manage Job Postings</h1>

            <div>
                <button id="addJobBtn" data-bs-toggle="modal" data-bs-target="#postJobModal" class="btn btn-success mb-3">
                    <i class="fas fa-circle-plus"></i> Add Job Posting
                </button>
                <button id="reloadTable" class="btn mb-3 ms-3">
                    <i class="fas fa-sync-alt"></i> Reload
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="jobTable" class="table table-bordered table-hovered w-100">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Job Title</th>
                        <th>Description</th>
                        <th>Company</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th>Expiry Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('page.job.employer.modal.edit')
@endsection

@section('scripts')
    <!-- DataTables and DataTables Responsive JS -->
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            let jobTable = $('#jobTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('job.listings') }}', // Update with your route
                columns: [{
                        data: 'logo',
                        name: 'logo',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'job_title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'company',
                        name: 'company'
                    },
                    {
                        data: 'salary',
                        name: 'salary'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date'
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
                jobTable.ajax.reload(null, false); // Reload the table without resetting the pagination
            });

            // Save Job Post
            $('#saveJobButton').on('click', function() {
                // Clear previous error messages
                $('.invalid-feedback').text('').removeClass('d-block');

                // Remove invalid class from all inputs
                $('.form-control').removeClass('is-invalid');

                // Disable the button and show spinner
                $(this).prop('disabled', true);
                $('#loadingSpinner').removeClass('d-none'); // Show spinner
                $('#buttonText').text('Saving...'); // Change button text

                // Create FormData object
                let formData = new FormData($('#jobPostForm')[0]);
                formData.append('is_active', $('#is_active').is(':checked') ? 1 :
                    0); // Include the checkbox
                formData.append('expiry_date', $('#expiry_date').val()); // Include the expiry date

                // Send the form data using AJAX
                $.ajax({
                    url: '{{ route('job.post') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Success notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Job posted successfully!',
                        }).then(() => {
                            $('#postJobModal').modal('hide');
                            $('#jobPostForm')[0].reset(); // Reset form fields
                        });

                        jobTable.ajax.reload(null,
                            false); // Reload the table without resetting the pagination
                    },
                    error: function(xhr) {
                        // Handle specific error codes
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                $('#' + key + 'Error').text(errors[key].join(' ')).addClass(
                                    'd-block'); // Show error for each field
                                $('#' + key).addClass(
                                    'is-invalid'); // Add invalid class to the input field
                            }
                        } else if (xhr.status === 500) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Something went wrong! Please try again later.',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred. Please try again.',
                            });
                        }
                    },
                    complete: function() {
                        // Hide the spinner and re-enable the button
                        $('#loadingSpinner').addClass('d-none'); // Hide spinner
                        $('#saveJobButton').prop('disabled', false); // Reset button state
                        $('#buttonText').text('Save'); // Reset button text
                    }
                });
            });

            // Event delegation for read more / read less
            $('#jobTable').on('click', '.read-more', function() {
                let $this = $(this);
                let $descriptionContainer = $this.closest('.description-container');
                let $fullDescription = $descriptionContainer.find('.full-description');
                let $truncatedDescription = $descriptionContainer.find('.description-text');

                if ($this.data('toggle') === 'description') {
                    if ($this.text() === 'Show More') {
                        $fullDescription.removeClass('d-none');
                        $this.text('Read Less');
                    } else {
                        $fullDescription.addClass('d-none');
                        $this.text('Show More');
                    }
                }
            });

            // Event delegation for delete job
            $('#jobTable').on('click', '.delete-job', function() {
                let jobId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Make AJAX call to delete the job
                        $.ajax({
                            url: '/api/v1/jobs/' + jobId, // Your delete route here
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'The job has been deleted.',

                                });
                                // Reload the DataTable
                                jobTable.ajax.reload(null,
                                    false
                                ); // Reload the table without resetting the pagination
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'There was an error deleting the job. Please try again.',
                                });
                            }
                        });
                    }
                });
            });

            // Event delegation for show job
            $('#jobTable').on('click', '.edit-job', function() {
                const jobId = $(this).data('id'); // Assuming you have a data attribute with job ID

                // Fetch job details via AJAX
                $.ajax({
                    url: `{{ route('job.get', ['job' => ':id']) }}`.replace(':id', jobId),
                    type: 'GET',
                    success: function(response) {
                        const job = response.data;

                        // Populate the modal fields with job data
                        $('#jobId').val(job.id);
                        $('#jobTitle').val(job.title);
                        $('#jobCompany').val(job.company);
                        $('#jobSalary_from').val(job.salary_from);
                        $('#jobSalary_to').val(job.salary_to);
                        $('#jobDescription').val(job.description);
                        $('#jobIs_active').prop('checked', job.is_active);
                        $('#jobExpiry_date').val(job.expiry_date).trigger('change');

                        // Show the update modal
                        $('#updateJobModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to retrieve job details. Please try again.',
                        });
                    }
                });
            });

            // Update Job Post
            $('#updateJobButton').on('click', function() {
                // Clear previous error messages
                $('.invalid-feedback').text('').removeClass('d-block');

                // Remove invalid class from all inputs
                $('.form-control').removeClass('is-invalid');

                // Disable the button and show spinner
                $(this).prop('disabled', true);
                $('#UpdateloadingSpinner').removeClass('d-none'); // Show spinner
                $('#buttonText').text('Updating...'); // Change button text

                // Create FormData object
                let formData = new FormData($('#jobUpdateForm')[0]);
                formData.append('jobIs_active', $('#jobIs_active').is(':checked') ? 1 :
                    0); // Include the checkbox
                formData.append('jobExpiry_date', $('#jobExpiry_date').val()); // Include the expiry date
                formData.append('_method', 'PUT'); // Include the expiry date

                // Send the form data using AJAX
                $.ajax({
                    url: '{{ route('job.update', ['job' => ':job']) }}'.replace(':job',
                        $('#jobId').val()), // Replace jobId with the actual job ID
                    type: 'POST', // Use PUT for updating
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Success notification
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Job updated successfully!',
                        }).then(() => {
                            $('#updateJobModal').modal('hide');
                            $('#jobUpdateForm')[0].reset(); // Reset form fields
                        });

                        jobTable.ajax.reload(null,
                            false); // Reload the table without resetting the pagination
                    },
                    error: function(xhr) {
                        // Handle specific error codes
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                $('#' + key + 'Error').text(errors[key].join(' ')).addClass(
                                    'd-block'); // Show error for each field
                                $('#' + key).addClass(
                                    'is-invalid'); // Add invalid class to the input field
                            }
                        } else if (xhr.status === 500) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Something went wrong! Please try again later.',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred. Please try again.',
                            });
                        }
                    },
                    complete: function() {
                        // Hide the spinner and re-enable the button
                        $('#UpdateloadingSpinner').addClass('d-none'); // Hide spinner
                        $('#updateJobButton').prop('disabled', false); // Reset button state
                        $('#buttonText').text('Update'); // Reset button text
                    }
                });
            });
        });
    </script>
@endsection
