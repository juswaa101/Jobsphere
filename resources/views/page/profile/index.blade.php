@extends('layouts.auth')

@section('title', 'Jobsphere - User Profile')

@section('styles')
    <!-- Include any necessary styles here -->
@endsection

@section('content')
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="fw-bold mb-4">User Profile</h1>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : 'https://placehold.co/100' }}"
                            alt="Profile Image" class="img-fluid rounded-circle mb-3" id="profileImagePreview">

                        <h5 class="card-title fw-bold mt-3">{{ auth()->user()->name }}</h5>
                        <p class="card-text fst-italic">{{ auth()->user()->email }}</p>

                        <!-- Profile Image Upload Form -->
                        <form id="profileUpdateForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="profileImage" class="form-label">Update Profile Image</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image"
                                    accept="image/*">
                                <div id="profile_imageError" class="invalid-feedback d-none"></div>
                                <!-- Error message for the input -->
                            </div>
                            <button type="button" id="saveProfileButton" class="btn btn-primary">Update Profile</button>
                        </form>

                        <!-- Remove Profile Image Button -->
                        @if (auth()->user()->profile_image)
                            <button type="button" id="removeProfileButton" class="btn btn-danger mt-3">Remove Profile
                                Image</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <!-- User Info Update Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title fw-bold">Update Name & Email</h5>
                    </div>
                    <div class="card-body">
                        <form id="userInfoUpdateForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ auth()->user()->name }}" required>
                                <div id="nameError" class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ auth()->user()->email }}" required>
                                <div id="emailError" class="invalid-feedback"></div>
                            </div>
                            <button type="button" id="saveUserInfoButton" class="btn btn-primary">Update Info</button>
                        </form>
                    </div>
                </div>

                <!-- Password Change Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title fw-bold">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form id="passwordChangeForm">
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="currentPassword" name="currentPassword"
                                    required>
                                <div id="currentPasswordError" class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                <div id="newPasswordError" class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                    required>
                                <div id="confirmPasswordError" class="invalid-feedback"></div>
                            </div>
                            <button type="button" id="savePasswordButton" class="btn btn-success">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Setup ajax csrf token per request
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Profile image preview
            $('#profileImage').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profileImagePreview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Update profile image
            $('#saveProfileButton').on('click', function() {
                let formData = new FormData($('#profileUpdateForm')[0]);

                // Disable the button and add a spinner
                $('#saveProfileButton').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: '{{ route('user.update.profile.image') }}', // Update profile image route
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Profile image updated successfully!',
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        // Clear previous validation errors
                        $('.invalid-feedback').addClass('d-none').html('');
                        $('.form-control').removeClass('is-invalid');

                        if (xhr.status === 422) {
                            // Handle validation errors
                            const errors = xhr.responseJSON
                                .errors; // Assuming Laravel returns errors in this format

                            for (let key in errors) {
                                console.log(key);

                                // Show error message below the input field
                                $('#' + key + 'Error').removeClass('d-none').html(errors[key]
                                    .join(' '));
                                $('#' + key).addClass('is-invalid'); // Highlight the input
                            }
                        } else {
                            // Handle other errors
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while updating the profile image.',
                            });
                        }
                    },
                    complete: function() {
                        // Enable the button and reset text
                        $('#saveProfileButton').prop('disabled', false).html('Update Profile');
                    }
                });
            });

            // Remove profile image
            $('#removeProfileButton').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This will remove your current profile image.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable the button and add a spinner
                        $('#removeProfileButton').prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Removing...');

                        $.ajax({
                            url: '{{ route('user.remove.profile.image') }}', // Route for removing profile image
                            type: 'POST',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Removed!',
                                    text: 'Profile image removed successfully!',
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while removing the profile image.',
                                });
                            },
                            complete: function() {
                                // Enable the button and reset text
                                $('#removeProfileButton').prop('disabled', false).html(
                                    'Remove Profile Image');
                            }
                        });
                    }
                });
            });

            // Save user info
            $('#saveUserInfoButton').on('click', function() {
                $('.invalid-feedback').text('').removeClass('d-block');
                $('.form-control').removeClass('is-invalid');

                let formData = new FormData($('#userInfoUpdateForm')[0]);

                // Disable the button and add a spinner
                $('#saveUserInfoButton').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: '{{ route('user.update') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'User info updated successfully!',
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                $('#' + key + 'Error').text(errors[key].join(' ')).addClass(
                                    'd-block');
                                $('#' + key).addClass('is-invalid');
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred. Please try again.',
                            });
                        }
                    },
                    complete: function() {
                        // Enable the button and reset text
                        $('#saveUserInfoButton').prop('disabled', false).html('Update Info');
                    }
                });
            });

            // Event handler for changing password
            $('#savePasswordButton').on('click', function() {
                $('.invalid-feedback').text('').removeClass('d-block');
                $('.form-control').removeClass('is-invalid');

                let formData = new FormData($('#passwordChangeForm')[0]);

                formData.append('_method', 'PUT');

                $('#savePasswordButton').prop('disabled', true);
                const $spinner = $(
                    '<span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>'
                );
                $('#savePasswordButton').append($spinner);

                $.ajax({
                    url: '{{ route('password.update') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Password Updated!',
                            text: response.message,
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                $('#' + key + 'Error').text(errors[key].join(' ')).addClass(
                                    'd-block');
                                $('#' + key).addClass('is-invalid');
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred.',
                            });
                        }
                    },
                    complete: function() {
                        $('#savePasswordButton').prop('disabled', false);
                        $spinner.remove();
                    }
                });
            });
        });
    </script>
@endsection
