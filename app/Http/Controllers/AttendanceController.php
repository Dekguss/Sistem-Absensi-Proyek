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
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i|after:check_in',
        ]);

        $checkOut = Carbon::createFromFormat('H:i', $request->check_out);
        $overtimeHours = 0;
        $countAsTwoDays = false;

        // Hitung lembur jika check out setelah jam 16:00
        if ($checkOut->gt(Carbon::createFromTime(16, 0))) {
            $overtimeHours = $checkOut->diffInHours(Carbon::createFromTime(16, 0));

            // Jika check out setelah jam 22:00, hitung sebagai 2 hari kerja
            if ($checkOut->gt(Carbon::createFromTime(22, 0))) {
                $countAsTwoDays = true;
            }
        }

        Attendance::create([
            'project_id' => $project->id,
            'worker_id' => $request->worker_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'overtime_hours' => $overtimeHours,
            'count_as_two_days' => $countAsTwoDays,
            'notes' => $request->notes,
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
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i|after:check_in',
        ]);

        $checkOut = Carbon::createFromFormat('H:i', $request->check_out);
        $overtimeHours = 0;
        $countAsTwoDays = false;

        if ($checkOut->gt(Carbon::createFromTime(16, 0))) {
            $overtimeHours = $checkOut->diffInHours(Carbon::createFromTime(16, 0));

            if ($checkOut->gt(Carbon::createFromTime(22, 0))) {
                $countAsTwoDays = true;
            }
        }

        $attendance->update([
            'worker_id' => $request->worker_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'overtime_hours' => $overtimeHours,
            'count_as_two_days' => $countAsTwoDays,
            'notes' => $request->notes,
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

    public function report(Project $project)
    {
        $attendances = Attendance::with('worker')
            ->where('project_id', $project->id)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date');

        return view('attendances.report', compact('project', 'attendances'));
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
                    'Keterangan' => $item->notes ?? '-',
                    'Gaji Harian' => 'Rp ' . number_format($item->worker->daily_salary, 0, ',', '.'),
                ];
            });

        $fileName = 'rekap_absensi_' . Str::slug($project->name) . '.xlsx';

        return (new FastExcel($attendances))->download($fileName);
    }
}
