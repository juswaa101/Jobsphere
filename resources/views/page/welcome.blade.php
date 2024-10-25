@extends('layouts.guest')

@section('title', 'Jobsphere - Home')

@section('styles')
    <style>
        .home-wrapper {
            display: flex;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 50px auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
        }

        .branding {
            flex: 1;
            background-image: url('{{ asset('assets/image/job-bg-register.jpg') }}');
            background-size: cover;
            background-position: center;
            padding: 60px;
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
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .branding h1,
        .branding p {
            position: relative;
            z-index: 2;
        }

        .branding h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .branding p {
            font-size: 1.75rem;
            margin-top: 20px;
        }

        .info-container {
            background-color: white;
            padding: 60px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-container h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .info-container p {
            font-size: 1.25rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            padding: 15px 30px;
            font-size: 1.25rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .branding {
                padding: 40px;
            }

            .info-container {
                padding: 40px;
            }

            .branding h1 {
                font-size: 3rem;
            }

            .branding p {
                font-size: 1.5rem;
            }

            .info-container h2 {
                font-size: 2rem;
            }

            .info-container p {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 576px) {
            .home-wrapper {
                flex-direction: column;
                max-width: 100%;
                margin: 20px;
            }

            .branding {
                padding: 30px;
                text-align: center;
            }

            .branding h1 {
                font-size: 2.5rem;
            }

            .branding p {
                font-size: 1.2rem;
            }

            .info-container {
                padding: 30px;
                text-align: center;
            }

            .info-container h2 {
                font-size: 1.75rem;
            }

            .info-container p {
                font-size: 1rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="home-wrapper">
            <div class="branding">
                <h1>Welcome to JobSphere</h1>
                <p>Your gateway to endless job opportunities!</p>
            </div>
            <div class="info-container">
                <h2>About Us</h2>
                <p>JobSphere connects job seekers with their dream jobs and helps employers find the best talent. Explore
                    our platform to discover new opportunities and grow your career.</p>
                @auth
                    <a href="{{ route('dashboard.home') }}" class="btn btn-primary">Get Started</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary">Register Now</a>
                @endauth
            </div>
        </div>
    </div>
@endsection
