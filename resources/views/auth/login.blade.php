@extends('layouts.guest')

@section('title', 'Jobsphere - Login')

@section('styles')
    <style>
        .login-wrapper {
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

        @media (max-width: 768px) {
            .branding {
                padding: 20px;
            }

            .form-container {
                padding: 20px;
            }

            .branding h1 {
                font-size: 2rem;
            }

            .branding p {
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .login-wrapper {
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
        <div class="login-wrapper">
            <div class="branding">
                <h1>Welcome Back to JobSphere</h1>
                <p>Log in to connect with your next opportunity!</p>
            </div>
            <div class="form-container">
                <div class="card">
                    <div class="text-center p-3">
                        <h2 class="fw-bold">Login</h2>
                    </div>
                    <div class="card-body">
                        @include('components.alerts.alert')
                        <form action="{{ route('auth.login') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="you@example.com" value="{{ old('email') }}">
                                @error('email')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter your password">
                                @error('password')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-warning w-100">Login</button>
                        </form>

                        <p class="text-center mt-3">Don't have an account? <a href="{{ route('register') }}"
                                class="footer-link">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
