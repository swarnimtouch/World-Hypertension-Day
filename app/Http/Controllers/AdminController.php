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


        return view('admin.dashboard', compact('totalEmployees', 'totalDoctors', 'totalDoctors1'));
    }


    public function listEmployees(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $search = $request->search;

        $employees = User::withCount('doctors') // 'doctors' = hasMany relationship
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('emp_code', 'like', "%{$search}%")
                ->orWhere('position_code', 'like', "%{$search}%")
                ->orWhere('designation', 'like', "%{$search}%")
                ->orWhere('hq_name', 'like', "%{$search}%")
                ->orWhere('hq_code', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.employees', compact('employees'));
    }

    public function deleteEmployee($id)
    {
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

        $userList = User::orderBy('name')->get(['id', 'name', 'emp_code']);
        return view('admin.banner', compact('userList')); // ✅ No $employees needed
    }

    public function getAllBanners(Request $request)
    {
        $query = Doctor::with('user');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('employee')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->employee}%")
                    ->orWhere('emp_code', 'like', "%{$request->employee}%");
            });
        }

        if ($request->ajax() && $request->has('draw')) {
            $totalRecords = Doctor::count();
            $filteredRecords = $query->count();

            $start  = $request->input('start', 0);
            $length = $request->input('length', 10);

            // Search
            $search = $request->input('search.value');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('degree', 'like', "%{$search}%")
                        ->orWhere('language', 'like', "%{$search}%")
                        ->orWhere('day', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%")
                                ->orWhere('emp_code', 'like', "%{$search}%");
                        });
                });
                $filteredRecords = $query->count();
            }

            $banners = $query->orderBy('id', 'desc')
                ->offset($start)
                ->limit($length)
                ->get();

            $data = $banners->map(function ($emp, $index) use ($start) {
                return [
                    'sr_no'         => $start + $index + 1,
                    'employee_name' => $emp->user ? $emp->user->name : 'N/A',
                    'user_code'     => $emp->user ? $emp->user->emp_code : 'N/A',
                    'name'          => $emp->name,
                    'degree'        => $emp->degree,
                    'language'      => ucwords($emp->language),
                    'day'           => $emp->day,
                    'banner_path'   => $emp->banner_path,
                    'id'            => $emp->id,
                ];
            });

            return response()->json([
                'draw'            => intval($request->input('draw')),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $data,
            ]);
        }

        return $query->get()->map(function ($doctor) {
            return [
                'employee_name' => $doctor->user ? $doctor->user->name : null,
                'user_code'     => $doctor->user ? $doctor->user->emp_code : null,
                'name'          => $doctor->name,
                'degree'        => $doctor->degree,
                'language'      => $doctor->language,
                'day'           => $doctor->day,
                'banner_path'   => $doctor->banner_path,
                'id'            => $doctor->id,
            ];
        });
    }


    public function listDoctors(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        if ($request->ajax()) {
            $query = MslDoctor::with('user');

            $searchValue = $request->input('search.value');
            if ($searchValue) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                        ->orWhere('msl_code', 'like', "%{$searchValue}%")
                        ->orWhere('city', 'like', "%{$searchValue}%")
                        ->orWhere('degree', 'like', "%{$searchValue}%")
                        ->orWhere('employee_code', 'like', "%{$searchValue}%")
                        ->orWhereHas('user', function ($subQ) use ($searchValue) {
                            $subQ->where('name', 'like', "%{$searchValue}%");
                        });
                });
            }

            $totalRecords = MslDoctor::count(); // Total records without filter
            $filteredRecords = $query->count(); // Total records with filter

            // Pagination from DataTables
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $employees = $query->orderBy('id', 'desc')
                ->offset($start)
                ->limit($length)
                ->get();

            $data = [];
            foreach ($employees as $index => $emp) {
                $data[] = [
                    'sr_no' => $start + $index + 1,
                    'emp_code' => '<span class="badge-mono emp">' . $emp->user->emp_code . '</span>',
                    'emp_name' => '<span style="font-weight:500;">' . ($emp->user ? $emp->user->name : 'N/A') . '</span>',
                    'doc_name' => '<div class="doc-name-cell"><span class="doc-name-text">' . $emp->name . '</span></div>',
                    'msl_code' => $emp->msl_code,
                    'city' => $emp->city,
                    'speciality' => '<span class="badge-mono">' . $emp->degree . '</span>',
                ];
            }

            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $data
            ]);
        }

        return view('admin.doctors');
    }


    public function getAllEmployees(Request $request)
    {
        $employees = User::withCount('doctors')
            ->get(['id', 'name', 'emp_code', 'position_code', 'designation', 'hq_name', 'hq_code'])
            ->toArray();

        return response()->json($employees);

    }

    public function getAllDoctors(Request $request)
    {
        $doctors = MslDoctor::with('user') // Assuming 'user' is the relationship method in MslDoctor
        ->get(['name', 'employee_code', 'msl_code', 'city', 'degree'])
            ->map(function ($doctor) {
                $doctor->employee_name = $doctor->user ? $doctor->user->name : null;
                return $doctor;
            });

        return response()->json($doctors);
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
