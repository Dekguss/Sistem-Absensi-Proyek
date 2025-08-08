<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th rowspan="2" class="align-middle">NAME</th>
            <th rowspan="2" class="align-middle">POSITION</th>
            @foreach($dates as $date)
            <th colspan="2" class="text-center text-uppercase" style="min-width: 100px;">
                <div>{{ strtoupper(\Carbon\Carbon::parse($date)->isoFormat('ddd')) }}</div>
            </th>
            @endforeach
            <th rowspan="2" class="align-middle text-center text-uppercase">TOTAL OT</th>
            <th rowspan="2" class="align-middle text-center text-uppercase">WORK DAY</th>
            <th rowspan="2" class="align-middle text-center text-uppercase">WAGE</th>
            <th rowspan="2" class="align-middle text-center text-uppercase">OT WAGE</th>
            <th rowspan="2" class="align-middle text-center text-uppercase">TOTAL WAGE</th>
            <th rowspan="2" class="align-middle text-center text-uppercase">TOTAL OVERTIME</th>
            <th rowspan="2" class="align-middle text-center text-uppercase">GRAND TOTAL</th>
        </tr>
        <tr>
            @foreach($dates as $date)
            <th class="text-center" style="min-width: 100px;">
                <div>{{ \Carbon\Carbon::parse($date)->format('d/m') }}</div>
            </th>
            <th class="text-center" style="min-width: 100px;">OT</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php
        // pengelompokan absensi berdasarkan pekerja
        $groupedAttendances = [];
        foreach ($attendances as $date => $dateAttendances) {
            foreach ($dateAttendances as $attendance) {
                $workerId = $attendance->worker_id;
                if (!isset($groupedAttendances[$workerId])) {
                    $groupedAttendances[$workerId] = [
                        'worker' => $attendance->worker,
                        'attendances' => [],
                        'total_ot' => 0,
                        'work_days' => 0,
                        'wage' => 0,
                        'ot_wage' => 0,
                        'total_wage' => 0,
                        'total_overtime' => 0,
                        'grand_total' => 0
                    ];
                }
                $groupedAttendances[$workerId]['attendances'][$date] = $attendance;
                $groupedAttendances[$workerId]['total_ot'] += $attendance->overtime_hours;

                // penghitungan work days (1 for full day, 0.5 for half day, etc.)
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

        // penghitungan gaji dan total
        foreach ($groupedAttendances as &$workerData) {
            $worker = $workerData['worker'];

            // Set OT wage based on role (20000 for mandor/tukang, 15000 for peladen)
            $otRate = in_array($worker->role, ['mandor', 'tukang']) ? 20000 : 15000;
            $dailyWage = $worker->daily_salary;

            $workerData['wage'] = $dailyWage;
            $workerData['ot_wage'] = $otRate;
            $workerData['total_wage'] = $dailyWage * $workerData['work_days'];
            $workerData['total_overtime'] = $workerData['total_ot'] * $otRate;
            $workerData['grand_total'] = $workerData['total_wage'] + $workerData['total_overtime'];
        }
        unset($workerData); // Break the reference
        @endphp

        @php
        // Urutkan pekerja berdasarkan peran: mandor -> tukang -> peladen
        $roleOrder = ['mandor' => 1, 'tukang' => 2, 'peladen' => 3];
        $sortedWorkers = $workers->sortBy(function($worker) use ($roleOrder) {
            return $roleOrder[strtolower($worker->role)] ?? 999;
        });
        @endphp

        @foreach($sortedWorkers as $worker)
            @php
            if (!isset($groupedAttendances[$worker->id])) {
                continue; // Skip jika worker tidak ada di groupedAttendances
            }
            $workerAttendances = $groupedAttendances[$worker->id]['attendances'];
            @endphp
            <tr>
                <td class="text-uppercase">{{ strtoupper($worker->name) }}</td>
                <td class="text-uppercase">{{ strtoupper($worker->role) }}</td>
                @foreach($dates as $date)
                    @php
                    $attendance = $workerAttendances[$date] ?? null;
                    $status = $attendance ? $attendance->status : '';
                    $overtime = $attendance ? $attendance->overtime_hours : 0;

                    $statusText = '';
                    $isAbsent = is_null($attendance) || $status === 'tidak_bekerja';

                    if ($status === '1_hari') {
                        $statusText = '1';
                    } elseif ($status === 'setengah_hari') {
                        $statusText = '0.5';
                    } elseif ($status === '1.5_hari') {
                        $statusText = '1.5';
                    } elseif ($status === '2_hari') {
                        $statusText = '2';
                    }
                    @endphp
                    <td data-is-absent="{{ $isAbsent ? 'true' : 'false' }}" class="text-center">
                        {{ strtoupper($statusText) }}
                    </td>
                    <td data-is-absent="{{ $isAbsent ? 'true' : 'false' }}" class="text-center">{{ $overtime ?: '' }}</td>
                @endforeach
                <td class="text-center">{{ number_format($groupedAttendances[$worker->id]['total_ot'], 1, ',', '.') }}</td>
                <td class="text-center">{{ number_format($groupedAttendances[$worker->id]['work_days'], 1, ',', '.') }}</td>
                <td class="text-end">{{ number_format($groupedAttendances[$worker->id]['wage'], 0, ',', '') }}</td>
                <td class="text-end">{{ number_format($groupedAttendances[$worker->id]['ot_wage'], 0, ',', '') }}</td>
                <td class="text-end">{{ number_format($groupedAttendances[$worker->id]['total_wage'], 0, ',', '') }}</td>
                <td class="text-end">{{ number_format($groupedAttendances[$worker->id]['total_overtime'], 0, ',', '') }}</td>
                <td class="text-end fw-bold">{{ number_format($groupedAttendances[$worker->id]['grand_total'], 0, ',', '') }}</td>
            </tr>
        @endforeach
        @php
        $grandTotal = collect($groupedAttendances)->sum('grand_total');
        @endphp
        <tr>
            <td colspan="{{ count($dates) * 2 + 8 }}" class="text-end" style="border: none;"></td>
            <td class="text-end fw-bold">{{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="{{ count($dates) * 2 + 8 }}" class="text-end" style="border: none;">Kasbon    </td>
            <td class="text-end fw-bold">-{{ number_format($kasbon, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="{{ count($dates) * 2 + 8 }}" class="text-end" style="border: none;">Total Payable    </td>
            <td class="text-end fw-bold">{{ number_format($grandTotal - $kasbon, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
