<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\MslDoctor;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LoginExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class AdminController extends Controller
{
        // Show login page
            public function showLogin()
            {
                return view('admin.login');
            }

            // Handle login form
            public function login(Request $request)
            {
                $username = $request->input('username');
                $password = $request->input('password');

                if ($username === 'admin' && $password === 'admin') {
                    session(['admin_logged_in' => true]);
                    return redirect()->route('admin.dashboard');
                }

                return back()->withErrors(['Invalid username or password.']);
            }

            // Dashboard
            public function dashboard()
            {
                if (!session('admin_logged_in')) {
                    return redirect()->route('admin.login');
                }
                $totalEmployees = User::count();
                $totalDoctors = MslDoctor::count(); // or use ->where('role', 'employee') if filtered
                $totalDoctors1 = Doctor::count(); // or use ->where('role', 'employee') if filtered

                // or use ->where('role', 'employee') if filtered

                return view('admin.dashboard', compact('totalEmployees','totalDoctors','totalDoctors1'));
            }


    public function listEmployees(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $search = $request->search;

        $employees = User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('emp_code', 'like', "%{$search}%")
                ->orWhere('position_code', 'like', "%{$search}%")
                ->orWhere('designation', 'like', "%{$search}%")
                ->orWhere('hq_name', 'like', "%{$search}%")
                ->orWhere('hq_code', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate(100)
            ->withQueryString();

        return view('admin.employees', compact('employees'));
    }

    public function deleteEmployee($id) {
                Doctor::findOrFail($id)->delete();
                return back()->with('success', 'Employee deleted.');
            }

            public function exportLogins()
            {
                return Excel::download(new LoginExport, 'employee_detail.xlsx');
            }

                public function listbanner(Request $request)

                {
                if (!session('admin_logged_in')) {
                    return redirect()->route('admin.login');
                }

                $search = $request->search;

                $employees = Doctor::with('user')
                    ->when($search, function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")          // Doctor Name
                        ->orWhere('degree', 'like', "%{$search}%")     // Speciality
                        ->orWhere('language', 'like', "%{$search}%")
                            ->orWhere('day', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")   // Employee Name
                                ->orWhere('emp_code', 'like', "%{$search}%");
                            });
                    })
                    ->orderBy('id', 'desc')
                    ->paginate(500)
                    ->withQueryString(); // ⭐ very important


                return view('admin.banner', compact('employees'));
            }




    public function listDoctors(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $search = $request->search;

        $employees = MslDoctor::with('user')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")          // Doctor Name
                ->orWhere('msl_code', 'like', "%{$search}%")   // MSL Code
                ->orWhere('city', 'like', "%{$search}%")       // City
                ->orWhere('degree', 'like', "%{$search}%")     // Specility
                ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");  // Employee Name
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(500)
            ->withQueryString(); // ⭐ pagination ke sath search preserve

        return view('admin.doctors', compact('employees'));
    }


    public function getAllEmployees(Request $request)
            {
                // Fetch all employees (no pagination)
                $employees = User::all(['name','emp_code','position_code','designation','hq_name','hq_code'])->toArray();

                return response()->json($employees); // Return as JSON
            }
            public function getAllDoctors(Request $request)
            {
                // Fetch all doctors with related user data (Employee name, etc.)
                $doctors = MslDoctor::with('user') // Assuming 'user' is the relationship method in MslDoctor
                                    ->get(['name', 'employee_code', 'msl_code', 'city', 'degree'])
                                    ->map(function($doctor) {
                                        // Attach employee name from related user
                                        $doctor->employee_name = $doctor->user ? $doctor->user->name : null;
                                        return $doctor;
                                    });

                // Convert data to an array and return as JSON
                return response()->json($doctors);
            }
            public function getAllBanners(Request $request)
            {
                $banners = Doctor::with('user')->get()->map(function ($doctor) {
                    $doctor->employee_name = $doctor->user ? $doctor->user->name : null;
                    $doctor->user_code = $doctor->user ? $doctor->user->emp_code : null;
                    return $doctor;
                });

                return response()->json($banners);
            }


            // Logout
            public function logout(Request $request)
            {
                $request->session()->forget('admin_logged_in');
                return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
            }
    public function delete($id)
    {
        $emp = Doctor::findOrFail($id);

        if ($emp->banner_path) {

            // ✅ URL ko S3 path me convert karo
            $path = parse_url($emp->banner_path, PHP_URL_PATH);
            $path = ltrim($path, '/'); // remove starting /

            Log::info('Attempt delete', ['path' => $path]);

            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);

                Log::info('Deleted from S3', ['path' => $path]);
            } else {
                Log::warning('File not found on S3', ['path' => $path]);
            }
        }

        $emp->delete();

        return back()->with('success', 'Deleted successfully');
    }
}
