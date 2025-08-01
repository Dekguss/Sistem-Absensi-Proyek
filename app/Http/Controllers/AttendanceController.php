<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use Spatie\Excel\Facades\Excel;

/**
 * Class AttendanceController
 * @package App\Http\Controllers
 */
class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project)
    {
        $attendances = Attendance::with('worker')
            ->where('project_id', $project->id)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date');

        return view('attendances.index', compact('project', 'attendances'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Project $project)
    {
        $workers = $project->workers;
        return view('attendances.create', compact('project', 'workers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'date' => 'required|date',
            'worker_id' => 'required|exists:workers,id',
            'status' => 'required|in:hadir,tidak_hadir,setengah_hari',
            'overtime_hours' => 'nullable|integer|min:0',
        ]);

        Attendance::create([
            'project_id' => $project->id,
            'worker_id' => $request->worker_id,
            'date' => $request->date,
            'status' => $request->status,
            'overtime_hours' => $request->overtime_hours ?? 0,
        ]);

        return redirect()
            ->route('projects.attendances.index', $project)
            ->with('success', 'Absensi berhasil dicatat');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project, Attendance $attendance)
    {
        $workers = $project->workers;
        return view('attendances.edit', compact('project', 'attendance', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project, Attendance $attendance)
    {
        $request->validate([
            'date' => 'required|date',
            'worker_id' => 'required|exists:workers,id',
            'status' => 'required|in:hadir,tidak_hadir,setengah_hari',
            'overtime_hours' => 'nullable|integer|min:0',
        ]);

        $attendance->update([
            'worker_id' => $request->worker_id,
            'date' => $request->date,
            'status' => $request->status,
            'overtime_hours' => $request->overtime_hours ?? 0,
        ]);

        return redirect()
            ->route('projects.attendances.index', $project)
            ->with('success', 'Absensi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project, Attendance $attendance)
    {
        $attendance->delete();
        return redirect()
            ->route('projects.attendances.index', $project)
            ->with('success', 'Absensi berhasil dihapus');
    }

    public function report(Request $request, Project $project)
    {
        // Set default date range to current week
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))
            : now()->startOfWeek();
            
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : now()->endOfWeek();

        $attendances = Attendance::with('worker')
            ->where('project_id', $project->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->get()
            ->groupBy('worker_id');

        return view('attendances.report', compact(
            'project', 
            'attendances',
            'startDate',
            'endDate'
        ));
    }

    public function export(Project $project)
    {
        $attendances = $project
            ->attendances()
            ->with('worker')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($item) {
                // Pastikan $item->date adalah instance Carbon
                $date = is_string($item->date)
                    ? \Carbon\Carbon::parse($item->date)
                    : $item->date;

                return [
                    'Tanggal' => $date->format('d/m/Y'),
                    'Nama' => $item->worker->name,
                    'Jabatan' => ucfirst($item->worker->role),
                    'Status' => $item->status,
                    'Gaji Harian' => 'Rp ' . number_format($item->worker->daily_salary, 0, ',', '.'),
                ];
            });

        $fileName = 'rekap_absensi_' . Str::slug($project->name) . '.xlsx';

        return (new FastExcel($attendances))->download($fileName);
    }
}
