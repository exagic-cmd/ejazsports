<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends BaseController
{
    /**
     * Show the login form page (standalone page if needed)
     */
    public function showLoginForm()
    {
        $footerData = $this->getFooterCategories();
        return view('auth.login', $footerData);
    }

    /**
     * Show the register form page (standalone page if needed)
     */
    public function showRegisterForm()
    {
        $footerData = $this->getFooterCategories();
        return view('auth.register', $footerData);
    }

    /**
     * Handle customer registration
     */
    public function register(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone_number' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.unique' => 'This email is already registered. Please login instead.',
            'password.confirmed' => 'Password and confirm password do not match.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $customerId = DB::table('customers')->insertGetId([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'status' => 1,
                'is_website_customer' => 1, // Website registered customer
                'store_id' => 1,
                'area_id' => 1,
                'opening_balance' => 0,
                'closing_balance' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log the customer in after registration
            $this->setCustomerSession($customerId, $request->first_name, $request->last_name, $request->email);

            if ($request->ajax() || $request->wantsJson()) {
                // Flash success message to session so it shows on redirect
                session()->flash('success', 'Registration successful! Welcome, ' . $request->first_name . '!');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Welcome, ' . $request->first_name . '!',
                    'redirect' => route('home')
                ]);
            }

            return redirect()->route('home')->with('success', 'Registration successful! Welcome, ' . $request->first_name . '!');

        } catch (\Exception $e) {
            \Log::error('Registration Error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed. Please try again.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Handle customer login
     */
    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $customer = DB::table('customers')
            ->where('email', $request->email)
            ->where('status', 1)
            ->where('is_website_customer', 1) // Only website customers can login
            ->first();

        if (!$customer) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No website account found with this email. Please register first.'
                ], 401);
            }
            return back()->withErrors(['email' => 'No website account found with this email. Please register first.'])->withInput();
        }

        if (!$customer->password) {
            // Customer exists but has no password (guest checkout customer)
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This account was created during checkout. Please register to set a password.'
                ], 401);
            }
            return back()->withErrors(['email' => 'This account was created during checkout. Please register to set a password.'])->withInput();
        }

        if (!Hash::check($request->password, $customer->password)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect password. Please try again.'
                ], 401);
            }
            return back()->withErrors(['password' => 'Incorrect password. Please try again.'])->withInput();
        }

        // Login successful
        $this->setCustomerSession($customer->id, $customer->first_name, $customer->last_name, $customer->email);

        if ($request->ajax() || $request->wantsJson()) {
            // Flash success message to session so it shows on redirect
            session()->flash('success', 'Welcome back, ' . $customer->first_name . '!');
            
            return response()->json([
                'success' => true,
                'message' => 'Welcome back, ' . $customer->first_name . '!',
                'redirect' => route('home')
            ]);
        }

        return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . $customer->first_name . '!');
    }

    /**
     * Handle customer logout
     */
    public function logout(Request $request)
    {
        session()->forget([
            'customer_id',
            'customer_first_name',
            'customer_last_name',
            'customer_email',
            'customer_phone',
            'customer_address'
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'You have been logged out.',
                'redirect' => route('home')
            ]);
        }

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    /**
     * Set customer session data
     */
    private function setCustomerSession($id, $firstName, $lastName, $email)
    {
        // Fetch full customer data for session
        $customer = DB::table('customers')->where('id', $id)->first();

        session([
            'customer_id' => $id,
            'customer_first_name' => $firstName,
            'customer_last_name' => $lastName,
            'customer_email' => $email,
            'customer_phone' => $customer->phone_number ?? null,
            'customer_address' => [
                'country' => $customer->country ?? null,
                'state' => $customer->state ?? null,
                'city' => $customer->city ?? null,
                'zip' => $customer->zip ?? null,
                'street_address' => $customer->street_address ?? null,
                'apt_suite' => $customer->apt_suite ?? null,
            ]
        ]);
    }

    /**
     * Get logged-in customer data for checkout auto-fill
     */
    public static function getCustomerDataForCheckout()
    {
        if (!session('customer_id')) {
            return null;
        }

        return [
            'first_name' => session('customer_first_name'),
            'last_name' => session('customer_last_name'),
            'email' => session('customer_email'),
            'phone' => session('customer_phone'),
            'country' => session('customer_address.country'),
            'state' => session('customer_address.state'),
            'city' => session('customer_address.city'),
            'zip' => session('customer_address.zip'),
            'street_address' => session('customer_address.street_address'),
            'apt_suite' => session('customer_address.apt_suite'),
        ];
    }
    /**
     * Show Forgot Password Form
     */
    public function showForgotPasswordForm()
    {
         return view('auth.forgot-password', $this->getFooterCategories());
    }

    /**
     * Send Reset Link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:customers,email']);

        $token = \Illuminate\Support\Str::random(64);

        // Uses 'password_reset_tokens' table (Laravel 10+ standard)
        // If older Laravel, use 'password_resets'
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\ResetPasswordEmail($token, $request->email));

        return back()->with('status', 'We have e-mailed your password reset link!');
    }

    /**
     * Show Reset Password Form
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email] + $this->getFooterCategories());
    }

    /**
     * Handle Password Reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
             return back()->withInput()->withErrors(['email' => 'Invalid token!']);
        }

        // Fetch customer to check legacy password
        $customer = DB::table('customers')->where('email', $request->email)->first();
        if ($customer && Hash::check($request->password, $customer->password)) {
             return back()->withInput()->withErrors(['password' => 'New password cannot be the same as your old password.']);
        }

        // Check token expiration (e.g. 60 mins)
        // if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) { ... }

        DB::table('customers')->where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();

        return redirect()->route('customer.login')->with('success', 'Your password has been changed!');
    }
}
