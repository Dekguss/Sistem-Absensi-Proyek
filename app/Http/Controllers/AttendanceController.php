<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Project;
use App\Models\Worker;
use App\Exports\AttendanceReportExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Spatie\Excel\Facades\Excel as SpatieExcel;

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

    public function report(Request $request)
    {
        $projects = Project::all();  // Get all projects for the dropdown

        // Set default dates
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $projectId = $request->input('project_id');

        $selectedProject = null;
        $attendances = collect();
        $dates = collect();

        if ($projectId) {
            $selectedProject = Project::findOrFail($projectId);

            $attendances = $selectedProject->attendances()
                ->with('worker')
                ->orderBy('date')
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->groupBy('date');

            $dates = $attendances->keys();
        }

        return view('attendances.report', compact(
            'projects',
            'selectedProject',
            'startDate',
            'endDate',
            'projectId',
            'attendances',
            'dates'
        ));
    }

    public function export(Request $request)
    {
        $projectId = $request->input('project_id');
        $startDateStr = $request->input('start_date');
        $endDateStr = $request->input('end_date');
        $kasbon = (float) $request->input('kasbon', 0);

        $project = Project::with('mandor')->findOrFail($projectId);
        $workers = $project->workers()->orderBy('name')->get();

        // Add mandor to the workers collection
        if ($project->mandor) {
            $workers->prepend($project->mandor);
        }

        $dates = [];
        $startDate = \Carbon\Carbon::parse($startDateStr);
        $endDate = \Carbon\Carbon::parse($endDateStr);
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->copy()->format('Y-m-d');
        }

        $attendances = Attendance::with('worker')->whereIn('worker_id', $workers->pluck('id'))
            ->whereBetween('date', [$startDateStr, $endDateStr])
            ->get()
            ->groupBy(function($att) {
                return \Carbon\Carbon::parse($att->date)->format('Y-m-d');
            });

        $projectName = $project->name;
        $fileName = 'Laporan Absensi - ' . $projectName . ' - ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y') . '.xlsx';

        $projects = Project::orderBy('name')->get();

        return Excel::download(new AttendanceReportExport(
            $attendances, 
            $dates, 
            $workers, 
            $projectName, 
            $projects, 
            $projectId,
            $kasbon,
            $startDate,
            $endDate
        ), $fileName);
    }

    /**
     * Get the label for attendance status
     *
     * @param string $status
     * @return string
     */
    private function getStatusLabel($status)
    {
        $statusLabels = [
            '1_hari' => '1 Hari',
            'setengah_hari' => 'Setengah Hari',
            '1.5_hari' => '1.5 Hari',
            '2_hari' => '2 Hari',
            'tidak_bekerja' => 'Tidak Bekerja'
        ];

        return $statusLabels[$status] ?? $status;
    }
}