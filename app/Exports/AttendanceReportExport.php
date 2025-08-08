<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceReportExport implements FromView, WithStyles, WithColumnWidths, WithEvents, WithColumnFormatting
{
    protected $attendances;
    protected $dates;
    protected $workers;
    protected $projectName;
    protected $projects;
    protected $projectId;
    protected $kasbon;

    public function __construct($attendances, $dates, $workers, $projectName, $projects, $projectId, $kasbon = 0)
    {
        $this->attendances = $attendances;
        $this->dates = $dates;
        $this->workers = $workers;
        $this->projectName = $projectName;
        $this->projects = $projects;
        $this->projectId = $projectId;
        $this->kasbon = (float) $kasbon;
    }

    public function view(): View
    {
        return view('attendances.report_excel', [
            'attendances' => $this->attendances,
            'dates' => $this->dates,
            'workers' => $this->workers,
            'projectName' => $this->projectName,
            'projects' => $this->projects,
            'projectId' => $this->projectId,
            'selectedProject' => $this->workers->first()->project ?? null,
            'kasbon' => $this->kasbon,
        ]);
    }

    public function columnWidths(): array
    {
        // Adjust column widths as needed
        $widths = ['A' => 20, 'B' => 15];
        $startColumn = 'C';
        foreach ($this->dates as $date) {
            $widths[$startColumn] = 5; // Date column
            $startColumn++;
            $widths[$startColumn] = 5; // OT column
            $startColumn++;
        }
        // Add widths for total columns
        $widths[$startColumn++] = 10; // TOTAL OT
        $widths[$startColumn++] = 10; // WORK DAY
        $widths[$startColumn++] = 15; // WAGE
        $widths[$startColumn++] = 15; // OT WAGE
        $widths[$startColumn++] = 15; // TOTAL WAGE
        $widths[$startColumn++] = 15; // TOTAL OVERTIME
        $widths[$startColumn++] = 15; // GRAND TOTAL
        return $widths;
    }

    public function columnFormats(): array
    {
        $formats = [];
        $startColumn = 'C';
        // Skip date columns
        foreach ($this->dates as $date) {
            $startColumn++;
            $startColumn++;
        }

        // Skip TOTAL OT and WORK DAY
        $startColumn++;
        $startColumn++;

        // Apply number format to currency columns
        $currencyFormat = '#,##0';
        $formats[$startColumn++] = $currencyFormat; // WAGE
        $formats[$startColumn++] = $currencyFormat; // OT WAGE
        $formats[$startColumn++] = $currencyFormat; // TOTAL WAGE
        $formats[$startColumn++] = $currencyFormat; // TOTAL OVERTIME
        $formats[$startColumn++] = $currencyFormat; // GRAND TOTAL

        return $formats;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Apply borders and alignment to all cells
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Remove border for the grand total, kasbon, and total payable rows
                $grandTotalLabelCol = 'A';
                $lastColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn) - 1;
                $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumnIndex);
                
                // Style for grand total, kasbon, and total payable rows
                $noBorderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ];

                // Apply style to grand total row (2 rows above the end)
                $grandTotalRow = $highestRow - 2;
                $sheet->getStyle($grandTotalLabelCol . $grandTotalRow . ':' . $lastColumnLetter . $grandTotalRow)
                    ->applyFromArray($noBorderStyle);

                // Apply style to kasbon row (1 row above the end)
                $kasbonRow = $highestRow - 1;
                $sheet->getStyle($grandTotalLabelCol . $kasbonRow . ':' . $lastColumnLetter . $kasbonRow)
                    ->applyFromArray($noBorderStyle);

                // Apply style to total payable row (last row)
                $totalPayableRow = $highestRow;
                $sheet->getStyle($grandTotalLabelCol . $totalPayableRow . ':' . $lastColumnLetter . $totalPayableRow)
                    ->applyFromArray($noBorderStyle);

                // Make total payable text bold
                $sheet->getStyle($grandTotalLabelCol . $totalPayableRow . ':' . $lastColumnLetter . $totalPayableRow)
                    ->getFont()
                    ->setBold(true);

                // --- Corrected Conditional Formatting Logic ---

                // 1. Re-group attendances by worker_id for easy lookup.
                $attendancesByWorker = [];
                foreach ($this->attendances as $date => $dateAttendances) {
                    foreach ($dateAttendances as $attendance) {
                        $attendancesByWorker[$attendance->worker_id][$date] = $attendance;
                    }
                }

                // 2. Sort workers in the exact same order as the Blade view.
                $roleOrder = ['mandor' => 1, 'tukang' => 2, 'peladen' => 3];
                $sortedWorkers = $this->workers->sortBy(function($worker) use ($roleOrder) {
                    return $roleOrder[strtolower($worker->role)] ?? 999;
                });

                $redFill = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFF0000'], // Red
                    ],
                ];

                // 3. Loop through sorted workers and apply styles row by row.
                $worker_row = 3; // Data starts at row 3
                foreach ($sortedWorkers as $worker) {
                    if (empty($attendancesByWorker[$worker->id])) {
                        continue;
                    }
                    $date_col_index = 3; // Date data starts at column 'C'
                    foreach ($this->dates as $date) {
                        $attendance = $attendancesByWorker[$worker->id][$date] ?? null;

                        // Condition for red background: no attendance record OR status is 'tidak_bekerja'
                        if (is_null($attendance) || $attendance->status === 'tidak_bekerja') {
                            $work_day_col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($date_col_index);
                            $ot_col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($date_col_index + 1);

                            $sheet->getStyle($work_day_col . $worker_row)->applyFromArray($redFill);
                            $sheet->getStyle($ot_col . $worker_row)->applyFromArray($redFill);
                        } else if (!is_null($attendance)) {
                            // Check for OT conditions
                            $ot_col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($date_col_index + 1);
                            if (empty($attendance->overtime_hours) || $attendance->overtime_hours == '0') {
                                $sheet->getStyle($ot_col . $worker_row)->applyFromArray($redFill);
                            }
                        }
                        $date_col_index += 2;
                    }
                    $worker_row++;
                }
            },
        ];
    }
}
