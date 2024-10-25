<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the dashboard page for authenticated users.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        // Get the current date
        $currentDate = now();

        // Start building the query
        $query = Job::where('is_active', 1)
            ->where('expiry_date', '>', $currentDate);

        // Apply filters if provided using `when`
        $query->when($request->salary_from, function ($q) use ($request) {
            if (is_numeric($request->salary_from)) {
                return $q->where('salary_from', '>=', $request->salary_from);
            }
        });

        $query->when($request->salary_to, function ($q) use ($request) {
            if (is_numeric($request->salary_to)) {
                return $q->where('salary_to', '<=', $request->salary_to);
            }
        });

        $query->when($request->company, function ($q) use ($request) {
            return $q->whereRaw('LOWER(company) LIKE ?', ['%' . strtolower($request->company) . '%']);
        });

        $query->when($request->title, function ($q) use ($request) {
            return $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($request->title) . '%']);
        });

        // Paginate results
        $jobPostings = $query->paginate(10); // Change 10 to however many items you want per page

        return view('page.dashboard', compact('jobPostings'));
    }
}
