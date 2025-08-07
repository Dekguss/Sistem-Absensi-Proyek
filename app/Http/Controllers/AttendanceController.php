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

    public function export(Request $request, Project $project)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get all dates in the range as strings
        $dates = collect();
        $currentDate = \Carbon\Carbon::parse($startDate);
        while ($currentDate->lte(\Carbon\Carbon::parse($endDate))) {
            $dates->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        // Get all workers for the project
        $workers = $project->workers;
        
        // Add mentor to workers collection if exists
        if ($project->mandor) {
            $mandor = $project->mandor;
            $mandor->role = 'mandor';  // Ensure role is set
            $workers->push($mandor);   // Add mentor to the workers collection
        }
        
        // Initialize attendance data for all workers with all dates
        $groupedAttendances = [];
        
        foreach ($workers as $worker) {
            $groupedAttendances[$worker->id] = [
                'worker' => $worker,
                'attendances' => [],
                'total_ot' => 0,
                'work_days' => 0,
                'wage' => 0,
                'ot_wage' => 0,
                'total_wage' => 0,
                'total_overtime' => 0,
                'grand_total' => 0
            ];
            
            // Initialize empty attendance for all dates
            foreach ($dates as $dateStr) {
                $groupedAttendances[$worker->id]['attendances'][$dateStr] = null;
            }
        }
        
        // Get all attendances for the date range and update the initialized data
        $attendances = $project->attendances()
            ->with('worker')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
            
        foreach ($attendances as $attendance) {
            $workerId = $attendance->worker_id;
            $dateStr = \Carbon\Carbon::parse($attendance->date)->format('Y-m-d');
            
            if (isset($groupedAttendances[$workerId])) {
                $groupedAttendances[$workerId]['attendances'][$dateStr] = $attendance;
                $groupedAttendances[$workerId]['total_ot'] += $attendance->overtime_hours;

                // Calculate work days
                $status = $attendance->status;
                $workDay = 0;
                if ($status === '1_hari') {
                    $workDay = 1;
                } elseif ($status === 'setengah_hari') {
                    $workDay = 0.5;
                } elseif ($status === '1.5_hari') {
                    $workDay = 1.5;
                } elseif ($status === '2_hari') {
                    $workDay = 2;
                }
                
                $groupedAttendances[$workerId]['work_days'] += $workDay;
            }
        }

        // Calculate wages and totals
        foreach ($groupedAttendances as &$workerData) {
            $worker = $workerData['worker'];
            $otRate = in_array($worker->role, ['mandor', 'tukang']) ? 20000 : 15000;
            $dailyWage = $worker->daily_salary;

            $workerData['wage'] = $dailyWage;
            $workerData['ot_wage'] = $otRate;
            $workerData['total_wage'] = $dailyWage * $workerData['work_days'];
            $workerData['total_overtime'] = $workerData['total_ot'] * $otRate;
            $workerData['grand_total'] = $workerData['total_wage'] + $workerData['total_overtime'];
        }
        unset($workerData);

        // Sort workers by role
        $sortedAttendances = collect($groupedAttendances)->sortBy(function($item) {
            $roleOrder = ['mandor' => 1, 'tukang' => 2, 'peladen' => 3];
            $role = strtolower($item['worker']->role);
            return $roleOrder[$role] ?? 999;
        });

        // Prepare data for Excel
        $excelData = [];
        
        // Add headers
        $header1 = ['NAME', 'POSITION'];
        $header2 = ['', ''];
        
        foreach ($dates as $dateStr) {
            $header1[] = \Carbon\Carbon::parse($dateStr)->isoFormat('ddd');
            $header1[] = '';
            $header2[] = \Carbon\Carbon::parse($dateStr)->format('d/m');
            $header2[] = 'OT';
        }
        
        $header1 = array_merge($header1, [
            'Total OT', 'Work Day', 'Wage', 'OT Wage', 'Total Wage', 'Total Overtime', 'Grand Total'
        ]);
        
        $header2 = array_merge($header2, array_fill(0, 7, ''));
        
        $excelData[] = $header1;
        $excelData[] = $header2;
        
        // Add data rows
        foreach ($sortedAttendances as $workerId => $data) {
            $worker = $data['worker'];
            $workerAttendances = $data['attendances'];
            
            $row = [
                strtoupper($worker->name),
                strtoupper($worker->role)
            ];
            
            foreach ($dates as $dateStr) {
                $attendance = $workerAttendances[$dateStr] ?? null;
                $status = $attendance ? $attendance->status : '';
                $overtime = $attendance ? $attendance->overtime_hours : 0;
                
                $statusText = '0'; // Default to 0 (tidak bekerja)
                if ($status === '1_hari') {
                    $statusText = '1';
                } elseif ($status === 'setengah_hari') {
                    $statusText = '0,5';
                } elseif ($status === '1.5_hari') {
                    $statusText = '1,5';
                } elseif ($status === '2_hari') {
                    $statusText = '2';
                }
                
                $row[] = $statusText;
                $row[] = $overtime > 0 ? number_format($overtime, 1, ',', '.') : '';
            }
            
            // Add calculated columns
            $row = array_merge($row, [
                number_format($data['total_ot'], 1, ',', '.'),
                number_format($data['work_days'], 1, ',', '.'),
                number_format($data['wage'], 0, ',', '.'),
                number_format($data['ot_wage'], 0, ',', '.'),
                number_format($data['total_wage'], 0, ',', '.'),
                number_format($data['total_overtime'], 0, ',', '.'),
                number_format($data['grand_total'], 0, ',', '.')
            ]);
            
            $excelData[] = $row;
        }
        
        // Generate filename
        $fileName = 'rekap_absensi_' . Str::slug($project->name) . '_' . 
                   \Carbon\Carbon::parse($startDate)->format('Ymd') . '_' . 
                   \Carbon\Carbon::parse($endDate)->format('Ymd') . '.xlsx';
        
        // Generate Excel file
        return (new FastExcel(collect($excelData)))->download($fileName);
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
