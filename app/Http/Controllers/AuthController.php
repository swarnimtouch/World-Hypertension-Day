<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'emp_code' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('emp_code', $request->emp_code)->first();

        if (!$user) {
            return back()->withErrors(['Employee code not found.']);
        }

        if ($request->password !== $request->emp_code) {
            return back()->withErrors(['Invalid password.']);
        }

        // ✅ Laravel Auth login
        Auth::login($user);

        return redirect()->route('dashboard');
    }




    public function dashboard()
    {
        if (!session('logged_in')) {
            return redirect()->route('login');
        }

        return view('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
