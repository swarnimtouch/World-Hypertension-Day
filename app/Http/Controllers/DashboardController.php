<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the authenticated user directly from Auth
        $user = Auth::user();
        // Get user's recent posters
        $recentPosters = Doctor::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard', [
            'employee' => $user,
            'recentPosters' => $recentPosters
        ]);
    }

    public function posterMessage($day)
    {
        return view('poster-message', compact('day'));
    }
}
