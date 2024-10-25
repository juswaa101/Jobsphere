<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show Login Page for Employer/Applicants
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function loginPage()
    {
        return view('auth.login');
    }

    /**
     * Authenticate for Employer/Applicants
     *
     * @param Request $request
     */
    public function loginAccount(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => ['required', 'email', 'string', 'exists:users,email'],
            'password' => ['required', 'min:8', 'max:32'],
        ]);

        try {
            // Get credentials from login
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];

            // Attempt to login the credentials of job seeker/employer
            if (!auth()->attempt($credentials)) {
                return back()->with('error', 'Credentials is not correct, enter correct details!');
            }

            // Redirect to dashboard/homepage if success
            return to_route('dashboard.home')
                ->with('success', 'Welcome ' . Auth::user()->name . '!');
        } catch (\Exception $e) {
            // Show error message on exception
            return back()->with('error', 'Something went wrong while authenticating your account!');
        }
    }

    /**
     * Logout employer/applicants
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function logout()
    {
        auth()->logout();

        return to_route('login');
    }
}
