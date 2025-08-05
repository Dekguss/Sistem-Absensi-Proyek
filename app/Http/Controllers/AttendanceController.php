<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Project;
use App\Models\Worker;
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
    public function index(Request $request)
    {
        $projects = Project::all();
        $workers = Worker::all();

        $query = Attendance::with('worker', 'project')
            ->latest();

        // Filter by project
        if ($request->has('project_id') && $request->project_id != '') {
            $query->where('project_id', $request->project_id);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('date', $request->date);
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $statusMap = [
                '1' => '1_hari',
                '0.5' => 'setengah_hari',
                '2' => '2_hari',
                '1.5' => '1.5_hari',
                '0' => 'tidak_bekerja'
            ];

            if (isset($statusMap[$request->status])) {
                $query->where('status', $statusMap[$request->status]);
            }
        }

        $attendances = $query->get();

        return view('attendances.index', compact('projects', 'attendances', 'workers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $projects = Project::all();
        $selectedProjectId = request('project_id');

        $workers = collect();

        if ($selectedProjectId) {
            $project = Project::with('mandor')->findOrFail($selectedProjectId);
            $workers = $project->workers;

            // Add mandor to the workers collection if exists
            if ($project->mandor) {
                $mandor = $project->mandor;
                $mandor->role = 'mandor';  // Ensure role is set
                $workers->prepend($mandor);  // Add mandor at the beginning
            }
        }

        return view('attendances.create', compact('projects', 'workers'));
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
            'project_id' => 'required|exists:projects,id',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:1_hari,setengah_hari,1.5_hari,tidak_bekerja,2_hari',
            'overtime' => 'required|array',
            'overtime.*' => 'required|in:0,1,2,3,4,5'
        ]);

        $projectId = $request->project_id;
        $attendanceDate = $request->attendance_date;

        // Delete existing attendance records for this project and date to prevent duplicates
        Attendance::where('project_id', $projectId)
            ->whereDate('date', $attendanceDate)
            ->delete();

        $attendanceData = [];

        foreach ($request->attendance as $workerId => $status) {
            $overtime = $request->overtime[$workerId] ?? 0;

            $attendanceData[] = [
                'project_id' => $projectId,
                'worker_id' => $workerId,
                'date' => $attendanceDate,
                'status' => $status,
                'overtime_hours' => $overtime,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all records at once for better performance
        if (!empty($attendanceData)) {
            Attendance::insert($attendanceData);
        }

        return redirect()
            ->route('attendances.create', [
                'project_id' => $projectId,
                'attendance_date' => $attendanceDate
            ])
            ->with('success', 'Data absensi berhasil disimpan');
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
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:1_hari,setengah_hari,1.5_hari,tidak_bekerja,2_hari',
            'overtime_hours' => 'nullable|numeric|min:0',
        ]);

        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'status' => $request->status,
            'overtime_hours' => $request->overtime_hours ?: 0,
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui');
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
            ->route('attendances.index')
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
