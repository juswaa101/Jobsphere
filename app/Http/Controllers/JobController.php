<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class JobController extends Controller
{
    /**
     * Show the Apply View Job Page
     *
     * @param Job $job
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function applyViewJob(Job $job)
    {
        // Check if the user has already applied for the job
        $alreadyApplied = Auth::user()
            ->jobs()
            ->where('job_id', $job->id)
            ->where('applicant_id', Auth::user()->id)
            ->exists();

        // Return the Apply View Page
        return view('page.apply-view', compact('job', 'alreadyApplied'));
    }

    /**
     * Show the Manage Job Listings
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function jobPostListingsPage()
    {
        // Authenticate the Request
        $this->authorize('accessJobPosting', Job::class);

        // Return the Employer's Job Listing
        return view('page.job.employer.listings');
    }

    /**
     * Show Job Listings for Employer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function jobPostListings(Request $request)
    {
        // Check if request is from ajax
        if (!$request->ajax()) {
            abort(403);
        }

        // Get all created job post by employer
        $jobListings = Job::query()
            ->with(
                [
                    'employer' => function ($query) {
                        $query->select('id', 'name', 'email');
                    }
                ]
            )
            ->where('user_created_by', Auth::user()->id)
            ->latest()
            ->get();


        return DataTables::of($jobListings)
            ->addColumn('logo', function ($job) {
                if (!$job->company_logo) {
                    return '';
                }

                // Get the URL for the company logo
                $url = Storage::url($job->company_logo);

                return '<img src="' . $url . '" alt="' . $job->company . '" style="width: 100px; height: auto;">';
            })
            ->addColumn('job_title', function ($job) {
                return '<span class="fw-bold text-uppercase">' . $job->title . '</span>';
            })
            ->addColumn('description', function ($job) {
                $truncatedDescription = str()->limit($job->description, 100); // Adjust the limit as needed
                return '
                    <div class="description-container">
                        <span class="description-text">' . $truncatedDescription . '</span>
                        <span class="full-description d-none">' . nl2br(e($job->description)) . '</span>
                        <button class="btn btn-link read-more" style="padding: 0;" data-toggle="description">
                            Show More
                        </button>
                    </div>
                ';
            })
            ->addColumn('company', function ($job) {
                return '<span class="fw-bold text-uppercase">' . $job->company . '</span>';
            })
            ->addColumn('salary', function ($job) {
                $salaryFrom = number_format($job->salary_from, 2);
                $salaryTo = number_format($job->salary_to, 2);
                return '₱' . $salaryFrom . ' - ₱' . $salaryTo; // Adding the PHP peso symbol
            })
            ->addColumn('status', function ($job) {
                return $job->is_active == 1 ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('expiry_date', function ($job) {
                return date('m/d/Y', strtotime($job->expiry_date));
            })
            ->addColumn('created_by', function ($job) {
                return $job->employer?->name;
            })
            ->addColumn('actions', function ($job) {
                return '
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-warning edit-job" data-id="' . $job->id . '" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-job" data-id="' . $job->id . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['logo', 'job_title', 'description', 'company', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Post a job (Employer)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postJob(Request $request)
    {
        // Authenticate the Request
        $this->authorize('accessJobPosting', Job::class);

        // Validate Job Posting Request
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'company' => ['required', 'string', 'max:255'],
            'salary_from' => ['required', 'numeric'],
            'salary_to' => ['required', 'numeric', 'gte:salary_from'],
            'company_logo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:8192'],
            'is_active' => ['nullable', 'boolean'],
            'expiry_date' => ['required', 'date', 'after:today'],
        ]);

        try {
            // Prepare the data for the job
            $jobData = [
                'title' => $request->title,
                'description' => $request->description,
                'company' => $request->company,
                'salary_from' => $request->salary_from,
                'salary_to' => $request->salary_to,
                'user_created_by' => Auth::user()->id,
                'is_active' => $request->is_active,
                'expiry_date' => $request->expiry_date,
            ];

            // Handle company logo upload if provided
            if ($request->hasFile('company_logo')) {
                $path = $request->file('company_logo')->store('company_logos', 'public');
                $jobData['company_logo'] = $path; // Store the path in the database
            }

            // Create a new job using the query builder
            Job::query()->create($jobData);

            return response()->json(['message' => 'Job posted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Job posting fails to post!'], 500);
        }
    }

    /**
     * Update a job (Employer)
     *
     * @param Request $request
     * @param Job $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateJob(Request $request, Job $job)
    {
        // Authenticate the Request
        $this->authorize('accessJobPosting', Job::class);

        // Validate Job Update Request
        $request->validate([
            'jobTitle' => ['required', 'string', 'max:255'],
            'jobDescription' => ['required', 'string'],
            'jobCompany' => ['required', 'string', 'max:255'],
            'jobSalary_from' => ['required', 'numeric'],
            'jobSalary_to' => ['required', 'numeric', 'gte:jobSalary_from'],
            'jobCompany_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:8192'],
            'jobIs_active' => ['nullable', 'boolean'],
            'jobExpiry_date' => ['required', 'date', 'after:today'],
        ]);

        try {
            // Prepare the data for the job update
            $jobData = [
                'title' => $request->jobTitle,
                'description' => $request->jobDescription,
                'company' => $request->jobCompany,
                'salary_from' => $request->jobSalary_from,
                'salary_to' => $request->jobSalary_to,
                'is_active' => $request->jobIs_active ? true : false,
                'expiry_date' => $request->jobExpiry_date,
            ];

            // Handle company logo upload if provided
            if ($request->hasFile('jobCompany_logo')) {

                // Optionally delete the old logo if needed
                if ($job->company_logo) {
                    // Define the path to the logo
                    $logoPath = $job->company_logo; // Relative path

                    // Check if the file exists and delete it
                    if (Storage::disk('public')->exists($logoPath)) {
                        Storage::disk('public')->delete($logoPath);
                    }
                }

                $path = $request->file('jobCompany_logo')->store('company_logos', 'public');
                $jobData['company_logo'] = $path; // Store the path in the database
            }

            // Update the job using the query builder
            $job->updateOrFail($jobData);

            return response()->json(['message' => 'Job updated successfully!']);
        } catch (\Exception $e) {
            // return response()->json(['message' => 'Job update failed!'], 500);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieve a job (Employer)
     *
     * @param Job $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrieveJob(Job $job)
    {
        // Authenticate the Request
        $this->authorize('accessJobPosting', Job::class);

        // Return the job posting details
        return response()->json([
            'data' => $job
        ]);
    }

    /**
     * Delete a job (Employer)
     *
     * @param Job $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyJob(Job $job)
    {
        // Authenticate the Request
        $this->authorize('accessJobPosting', Job::class);

        try {
            // Optionally delete the old logo if needed
            if ($job->company_logo) {
                // Define the path to the logo
                $logoPath = $job->company_logo; // Relative path

                // Check if the file exists and delete it
                if (Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
            }

            // Delete the job posting
            $job->deleteOrFail();

            return response()->json(['message' => 'Job deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Job posting fails to delete!'], 500);
        }
    }
}
