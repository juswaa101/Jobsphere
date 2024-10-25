<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Observers\JobObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class JobApplicationController extends Controller
{
    /**
     * Submit Application for Job
     *
     * @param Request $request
     * @param Job $job
     * @return @return \Illuminate\Http\RedirectResponse
     */
    public function apply(Request $request, Job $job)
    {
        // Check if the user has already applied for the job
        $alreadyApplied = Auth::user()
            ->jobs()
            ->where('job_id', $job->id)
            ->where('applicant_id', Auth::user()->id)
            ->exists();

        if ($alreadyApplied) {
            return redirect()->back()->withErrors(['resume' => 'You have already submitted an application for this job.']);
        }

        // Validate the request
        $request->validate([
            'cover_letter' => ['nullable', 'string'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
        ]);

        try {
            // Handle DB transactions for Job Application
            DB::transaction(function () use ($job, $request) {
                // Handle the file upload
                if ($request->hasFile('resume')) {
                    $resumePath = $request->file('resume')->store('resumes', 'public');
                }

                // Attach the application to the user and job
                Auth::user()->jobs()->attach($job->id, [
                    'cover_letter' => $request->input('cover_letter'),
                    'resume_cv' => $resumePath,
                    'job_status' => Job::STATUS_PENDING, // You can adjust the status as needed
                ]);

                // Send Job Application Request thru Mail via Job Observer
                $jobObserver = new JobObserver();
                $jobObserver->attached(Auth::user(), $job);
            });

            // Redirect back with success message
            return redirect()->route('dashboard.home')->with('success', 'Application submitted successfully!');
        } catch (\Exception $e) {
            // Redirect back with error message
            return back()->with('error', 'Something went wrong while submitting your application, please try again!');
        }
    }

    /**
     * List of submitted applications for the employer
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function submittedApplications(Request $request)
    {
        $query = Auth::user()->jobs()->withPivot('cover_letter', 'resume_cv', 'job_status')
            ->select('jobs.id', 'jobs.title', 'jobs.company', 'jobs.company_logo', 'job_user.job_status')
            ->join('job_user AS ju', 'jobs.id', '=', 'ju.job_id') // Use an alias for job_user
            ->where('ju.applicant_id', Auth::user()->id); // Use the alias here as well

        // Use Eloquent's when method for conditional filtering
        $query->when($request->filled("status"), function ($query) use ($request) {
            return $query->where('ju.job_status', $request->status); // Use the alias here
        });

        $applications = $query->paginate(10);

        return view('page.job.job-seeker.jobs-listing', compact('applications'));
    }

    /**
     * Show job application details
     *
     * @param Job $job
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Job $job)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Retrieve the job application for the authenticated user
        $application = $user->jobs()->where('job_id', $job->id)->firstOrFail();

        // Pass the application and job details to the view
        return view('page.job.job-seeker.applications-details', compact('application'));
    }

    /**
     * Show job review application page
     */
    public function jobReviewApplicationPage()
    {
        // Authorize Job Review Application Page
        $this->authorize('accessJobApplication', Job::class);

        // Return job review application page
        return view('page.job.employer.job-application-review');
    }

    /**
     * Show Job Listings for Employer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function jobApplicantListings(Request $request)
    {
        // Check if the request is from AJAX
        if (!$request->ajax()) {
            abort(403);
        }

        // Get all jobs created by the employer with their applicants
        $jobs = Job::query()
            ->with(['applicants' => function ($query) {
                $query->select('applicant_id', 'name', 'email', 'resume_cv', 'job_status') // Select necessary fields
                    ->withPivot('created_at'); // Include pivot data if needed
            }])
            ->where('user_created_by', Auth::user()->id)
            ->get();

        // Flatten the data for DataTables
        $applicantData = [];
        foreach ($jobs as $job) {
            foreach ($job->applicants as $applicant) {
                $applicantData[] = [
                    'job_id' => $job->id,
                    'job_title' => $job->title,
                    'company' => $job->company,
                    'company_logo' => $job->company_logo,
                    'applicant_id' => $applicant->applicant_id,
                    'applicant_name' => $applicant->name,
                    'applicant_email' => $applicant->email,
                    'resume_cv' => $applicant->resume_cv,
                    'job_status' => $applicant->pivot->job_status,
                    'applied_on' => $applicant->pivot->created_at,
                ];
            }
        }

        return DataTables::of($applicantData)
            ->addColumn('logo', function ($applicant) {
                return $applicant['company_logo'] ? '<img src="' . Storage::url($applicant['company_logo']) . '" alt="Company Logo" style="width: 100px; height: auto;">' : '';
            })
            ->addColumn('job_title', function ($applicant) {
                return '<span class="fw-bold">' . e($applicant['job_title']) . '</span>';
            })
            ->addColumn('company', function ($applicant) {
                return '<span class="fw-bold">' . e($applicant['company']) . '</span>';
            })
            ->addColumn('applicant_name', function ($applicant) {
                return '<span class="fw-bold">' . e($applicant['applicant_name']) . '</span>';
            })
            ->addColumn('applicant_email', function ($applicant) {
                return '<span>' . e($applicant['applicant_email']) . '</span>';
            })
            ->addColumn('resume', function ($applicant) {
                return '<button class="btn btn-link view-resume" data-job-id="' . $applicant['job_id'] . '" data-user-id="' . $applicant['applicant_id'] . '" data-resume-link="' . $applicant['resume_cv'] . '">View Resume</button>';
            })
            ->addColumn('status', function ($applicant) {
                $statusOptions = [
                    0 => 'Pending',
                    1 => 'Viewed',
                    2 => 'Interviewed',
                    3 => 'Hired',
                    4 => 'Not Moving Forward'
                ];

                // Create the dropdown
                $dropdown = '<select class="form-select change-status" data-job-id="' . $applicant['job_id'] . '" data-user-id="' . $applicant['applicant_id'] . '" name="application_status">';
                foreach ($statusOptions as $value => $label) {
                    $selected = $value == $applicant['job_status'] ? 'selected' : '';
                    $dropdown .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                }
                $dropdown .= '</select>';

                return $dropdown;
            })
            ->addColumn('applied_on', function ($applicant) {
                return date('m/d/Y', strtotime($applicant['applied_on']));
            })
            ->addColumn('actions', function ($applicant) {
                return '<div class="btn-group" role="group">
                <button class="btn btn-sm btn-danger delete-application"  data-job-id="' . $applicant['job_id'] . '" data-user-id="' . $applicant['applicant_id'] . '" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>';
            })
            ->rawColumns(['logo', 'company', 'job_title', 'applicant_name', 'applicant_email', 'resume', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Show Resume
     *
     * @param Job $job
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function showResume(Job $job, User $user)
    {
        // Load the user's jobs with the pivot data
        $applicant = User::query()
            ->with('jobs')
            ->findOrFail($user->id);

        // Get the first job and its pivot data
        $job = $applicant->jobs->where('id', $job->id)->first();

        return response()->json([
            'data' => [
                'name' => $applicant->name,
                'email' => $applicant->email,
                'resume_cv' => $job->pivot->resume_cv ? Storage::url($job->pivot->resume_cv) : null, // Resume URL
                'job_title' => $job->title ?? 'N/A', // Job title applied for
                'company' => $job->company ?? 'N/A' // Company applied to
            ]
        ]);
    }

    /**
     * Update Job Status Application
     *
     * @param Request $request
     * @param User $user
     * @param Job $job
     */
    public function updateStatusApplication(Request $request, User $user, Job $job)
    {
        // Authorize the employer
        $this->authorize('accessJobUpdateStatus', $job);

        try {
            // Handle DB transactions for Application update status
            DB::transaction(function () use ($user, $job, $request) {
                // Update status of application of the applicant
                $user->jobs()->updateExistingPivot(
                    $job->id,
                    ['job_status' => $request->application_status]
                );

                // Call manually the observer for any changes in the model
                $jobObserver = new JobObserver();
                $jobObserver->updateStatus($user, $job);
            });

            // Throw 200 Success
            return response()->json([
                'data' => 'Application Status Updated!'
            ]);
        } catch (\Exception $e) {
            // Throw 500 API Error
            return response()->json([
                'data' => 'Application Status Fails to Update!'
            ], 500);
        }
    }

    /**
     * Delete Job Application
     *
     * @param Request $request
     * @param User $user
     * @param Job $job
     */
    public function deleteJobApplication(Request $request, User $user, Job $job)
    {
        // Authorize the employer
        $this->authorize('accessJobUpdateStatus', $job);

        try {
            $application = $user->jobs()->where('job_id', $job->id)->first();

            // If application does not exist, return early
            if (!$application) {
                return response()->json([
                    'data' => 'Application not found!'
                ], 404);
            }

            // Check if resume_cv exists
            if ($application->pivot->resume_cv) {
                $resumePath = $application->pivot->resume_cv;

                // Check if the file exists and delete it
                if (Storage::disk('public')->exists($resumePath)) {
                    Storage::disk('public')->delete($resumePath);
                }
            }

            // Delete the application of the applicant
            $user->jobs()->detach($job->id);

            // Throw 200 Success
            return response()->json([
                'data' => 'Application Status Deleted!'
            ]);
        } catch (\Exception $e) {
            // Throw 500 API Error
            return response()->json([
                'data' => 'Application Status Failed to Delete!'
            ], 500);
        }
    }
}
