<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes (Login, Register)
Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [LoginController::class, 'loginPage'])
        ->name('login');
    Route::post('/login', [LoginController::class, 'loginAccount'])
        ->name('auth.login');

    Route::get('/register', [RegistrationController::class, 'registrationPage'])
        ->name('register');
    Route::post('/register', [RegistrationController::class, 'registerAccount'])
        ->name('auth.register');
});
// End of Authentication Routes

// Authenticated Routes (Dashboard, Job Posting)
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.home');

    // Job Post Listing Page
    Route::get('/jobs-post-listings', [JobController::class, 'jobPostListingsPage'])
        ->name('job.post.listings');

    // Apply View Job
    Route::get('/jobs/apply/{job}', [JobController::class, 'applyViewJob'])
        ->name('job.apply');

    // Apply Job
    Route::post('/jobs/apply/{job}/submit', [JobApplicationController::class, 'apply'])
        ->name('job.apply.submit');

    // My Jobs
    Route::get('/my-jobs', [JobApplicationController::class, 'submittedApplications'])
        ->name('job.own');

    // Job Application Details
    Route::get('/application/details/{job}', [JobApplicationController::class, 'show'])
        ->name('application.details');

    // Employer Job Posting and Listings API
    Route::group(['middleware' => 'employer', 'prefix' => 'api/v1'], function () {
        Route::get('/jobs', [JobController::class, 'jobPostListings'])
            ->name('job.listings');
        Route::post('/jobs', [JobController::class, 'postJob'])
            ->name('job.post');
        Route::get('/jobs/{job}', [JobController::class, 'retrieveJob'])
            ->name('job.get');
        Route::put('/jobs/{job}', [JobController::class, 'updateJob'])
            ->name('job.update');
        Route::delete('/jobs/{job}', [JobController::class, 'destroyJob'])
            ->name('job.destroy');

        // Show listings of applicants per employer
        Route::get('/job/applicants', [JobApplicationController::class, 'jobApplicantListings'])
            ->name('job.application-listings');

        // Show resume of applicant in listing of employer
        Route::get('/job/applicants/{job}/{user}', [JobApplicationController::class, 'showResume'])
            ->name('job.get.application');

        // Update the application status of the job applicant
        Route::patch('/jobs/status/update/{user}/{job}', [JobApplicationController::class, 'updateStatusApplication'])
            ->name('update.application');

        // Delete the job application of the job applicant
        Route::delete('/jobs/status/destroy/{user}/{job}', [JobApplicationController::class, 'deleteJobApplication'])
            ->name('delete.application');
    });

    // View Profile Page
    Route::get('/profile', [ProfileController::class, 'viewProfile'])
        ->name('view.profile');

    // Update the user info (name and email)
    Route::post('/user/update', [ProfileController::class, 'update'])->name('user.update');

    // Update the profile image
    Route::post('/user/update-profile-image', [ProfileController::class, 'updateProfileImage'])->name('user.update.profile.image');
    Route::post('/user/remove-profile-image', [ProfileController::class, 'removeProfileImage'])->name('user.remove.profile.image');

    // Update Password
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('password.update');

    // Review Job Application for Candidates
    Route::get('/job/review', [JobApplicationController::class, 'jobReviewApplicationPage'])
        ->name('job.review');

    // Logout Authentication
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('auth.logout');
});
// End of Authenticated Routes
