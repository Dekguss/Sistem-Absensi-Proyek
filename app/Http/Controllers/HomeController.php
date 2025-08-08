<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Attendance;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $workers = Worker::all();
        $projects = Project::all();
        
        // Get today's date
        $today = Carbon::today()->toDateString();
        
        // Get all attendances for today
        $attendances = Attendance::whereDate('date', $today)->get();
        
        // Count different attendance statuses
        $presentCount = $attendances->whereIn('status', ['1_hari', 'setengah_hari', '2_hari', '1.5_hari'])->count();
        $absentCount = $attendances->where('status', 'tidak_bekerja')->count();
        
        return view('dashboard', compact(
            'workers', 
            'projects', 
            'attendances',
            'presentCount',
            'absentCount'
        ));
    }
}
