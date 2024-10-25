<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /**
     * Show Registration Page for Employer/Applicants
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function registrationPage()
    {
        return view('auth.register');
    }

    /**
     * Registration for Employer/Applicants
     *
     * @param Request $request
     */
    public function registerAccount(Request $request)
    {
        // Validate the registration request
        $request->validate([
            'role' => ['required', 'in:1,2'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'string', 'unique:users,email'],
            'password' => ['required', 'min:8', 'max:32'],
            'password_confirmation' => ['same:password'],
        ]);

        try {
            // Create the user either job seeker/employer
            User::query()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role // 1-Job Seeker, 2-Employer
            ]);

            // Show success message on submit
            return back()->with('success', 'Account created, please login!');
        } catch (\Exception $e) {
            // Show error message on exception
            return back()->with('error', 'Something went wrong while creating your account!');
        }
    }
}
