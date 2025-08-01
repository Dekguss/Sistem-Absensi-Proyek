<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Worker;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::with('mandor')->get();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Ambil daftar mandor yang belum memiliki proyek
        $mandor = Worker::where('role', 'mandor')
            ->whereDoesntHave('managedProjects')
            ->get();
    
        // Ambil daftar pekerja (tukang) yang belum terdaftar di proyek manapun
        $workers = Worker::where('role', 'tukang')
            ->whereDoesntHave('projects')
            ->get();
    
        return view('projects.create', compact('mandor', 'workers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'mandor_id' => 'required|exists:workers,id',
            'workers' => 'required|array',
            'workers.*' => 'exists:workers,id'
        ]);

        $project = Project::create($request->only([
            'name',
            'description',
            'start_date',
            'end_date',
            'mandor_id',
        ]));

        $project->workers()->attach($request->workers);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil ditambahkan!!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $project->load('mandor', 'workers', 'attendances.worker');
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        // Ambil daftar mandor yang belum memiliki proyek ATAU mandor yang sedang mengelola proyek ini
        $mandor = Worker::where('role', 'mandor')
            ->where(function($query) use ($project) {
                $query->whereDoesntHave('managedProjects')
                      ->orWhere('id', $project->mandor_id);
            })
            ->get();

        // Ambil daftar pekerja (semua role kecuali mandor) yang belum terdaftar di proyek manapun
        // ATAU yang sudah terdaftar di proyek ini
        $workers = Worker::where('role', '!=', 'mandor')
            ->where(function($query) use ($project) {
                $query->whereDoesntHave('projects')
                      ->orWhereHas('projects', function($q) use ($project) {
                          $q->where('project_id', $project->id);
                      });
            })
            ->get();

        // Load relasi workers untuk proyek ini
        $project->load('workers');
        
        return view('projects.edit', compact('project', 'mandor', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'mandor_id' => 'required|exists:workers,id',
            'workers' => 'required|array',
            'workers.*' => 'exists:workers,id'
        ]);

        $project->update($request->only([
            'name',
            'description',
            'start_date',
            'end_date',
            'mandor_id',
        ]));

        $project->workers()->sync($request->workers);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil diperbarui!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus!!');
    }
}
