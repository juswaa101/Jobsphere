@extends('layouts.guest')

@section('title', 'Jobsphere - Register')

@section('styles')
    <style>
        .registration-wrapper {
            display: flex;
            flex-wrap: wrap;
            /* Allow wrapping for smaller screens */
            max-width: 900px;
            margin: 50px auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .branding {
            flex: 1;
            background-image: url('{{ asset('assets/image/job-bg-register.jpg') }}');
            background-size: cover;
            background-position: center;
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
            position: relative;
        }

        .branding::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .branding h1,
        .branding p {
            position: relative;
            z-index: 2;
        }

        .branding h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .branding p {
            font-size: 1.2rem;
            margin-top: 20px;
        }

        .form-container {
            background-color: white;
            padding: 40px;
            flex: 1;
        }

        .card {
            border: none;
            box-shadow: none;
        }

        .btn-warning {
            background-color: #FFC107;
            color: #212529;
            font-weight: bold;
        }

        .text-danger {
            font-size: 0.85rem;
        }

        .footer-link {
            color: #2C3E50;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .branding {
                padding: 20px;
                /* Reduce padding for smaller screens */
            }

            .form-container {
                padding: 20px;
                /* Reduce padding for smaller screens */
            }

            .branding h1 {
                font-size: 2rem;
                /* Adjust heading size */
            }

            .branding p {
                font-size: 1rem;
                /* Adjust paragraph size */
            }
        }

        @media (max-width: 576px) {
            .registration-wrapper {
                flex-direction: column;
                /* Stack branding and form on small screens */
                max-width: 100%;
                /* Full width on mobile */
                margin: 20px;
                /* Reduce margin */
            }

            .branding {
                padding: 30px;
                /* Adjust padding for mobile */
                text-align: center;
                /* Center align text on mobile */
            }

            .branding h1 {
                font-size: 1.8rem;
                /* Smaller heading on mobile */
            }

            .branding p {
                font-size: 0.9rem;
                /* Smaller paragraph on mobile */
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="registration-wrapper">
            <div class="branding">
                <h1>Join JobSphere</h1>
                <p>Your career starts here. Connect with employers and find your dream job!</p>
            </div>
            <div class="form-container">
                <div class="card">
                    <div class="text-center p-3 mt-3">
                        <h2 class="fw-bold">Register</h2>
                    </div>
                    <div class="card-body">
                        @include('components.alerts.alert')
                        <form action="{{ route('auth.register') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="role" class="form-label">Register as:</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select your role
                                    </option>
                                    <option value="1" {{ old('role') == '1' ? 'selected' : '' }}>Job Seeker</option>
                                    <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>Employer</option>
                                </select>
                                @error('role')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="John Doe" required>
                                @error('name')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}" placeholder="you@example.com" required>
                                @error('email')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Choose a strong password" required>
                                @error('password')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Re-enter your password" required>
                                @error('password_confirmation')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-warning w-100">Register</button>
                        </form>

                        <p class="text-center mt-3">Already have an account? <a href="{{ route('login') }}"
                                class="footer-link">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
