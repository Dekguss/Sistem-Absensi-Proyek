<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Worker;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $workers = Worker::all();
        $projects = Project::all();
        return view('dashboard', compact('workers', 'projects'));
    }
}
