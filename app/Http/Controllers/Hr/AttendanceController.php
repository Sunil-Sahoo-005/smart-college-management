<?php

namespace App\Http\Controllers\Hr;

use App\Exports\HrAttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(string $tenantKey, Request $request)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $today = Carbon::today()->toDateString();
        $date = $request->filled('date') ? $request->date : $today;

        $departments = Department::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $designations = Designation::where('tenant_id', $tenant->id)->orderBy('name')->get();

        $staffQuery = Staff::where('tenant_id', $tenant->id)
            ->with(['designation', 'department']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $staffQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $staffQuery->where('department_id', $request->department_id);
        }

        if ($request->filled('designation_id')) {
            $staffQuery->where('designation_id', $request->designation_id);
        }

        $staffList = $staffQuery
            ->orderBy('first_name')
            ->paginate(25)
            ->withQueryString();

        $attendanceMap = Attendance::where('tenant_id', $tenant->id)
            ->whereDate('attendance_date', $date)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->get()
            ->keyBy('staff_id');

        $attendances = $staffList->getCollection()->map(function ($staff) use ($attendanceMap, $date) {
            $attendance = $attendanceMap->get($staff->id);

            if ($attendance) {
                return $attendance->setRelation('staff', $staff);
            }

            $attendance = new Attendance([
                'tenant_id' => $staff->tenant_id,
                'staff_id' => $staff->id,
                'attendance_date' => $date,
                'check_in' => null,
                'check_out' => null,
                'working_minutes' => 0,
                'is_late' => false,
                'status' => 'absent',
                'source' => 'manual',
                'remark' => null,
            ]);

            $attendance->setRelation('staff', $staff);

            return $attendance;
        });

        $stats = [
            'present' => $attendanceMap->where('status', 'present')->count(),
            'absent' => $staffList->total() - $attendanceMap->count(),
            'half_day' => $attendanceMap->where('status', 'half_day')->count(),
            'leave' => $attendanceMap->where('status', 'leave')->count(),
        ];

        $attendances = new \Illuminate\Pagination\LengthAwarePaginator(
            $attendances,
            $staffList->total(),
            $staffList->perPage(),
            $staffList->currentPage(),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('hr.attendance.index', compact(
            'tenant',
            'tenantKey',
            'date',
            'today',
            'departments',
            'designations',
            'attendances',
            'stats'
        ));
    }

    public function export(string $tenantKey, Request $request)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $date = $request->filled('date') ? $request->date : Carbon::today()->toDateString();

        $staffQuery = Staff::where('tenant_id', $tenant->id)
            ->with(['designation', 'department']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $staffQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $staffQuery->where('department_id', $request->department_id);
        }

        if ($request->filled('designation_id')) {
            $staffQuery->where('designation_id', $request->designation_id);
        }

        $staff = $staffQuery->orderBy('first_name')->get();
        $attendanceMap = Attendance::where('tenant_id', $tenant->id)
            ->whereDate('attendance_date', $date)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->get()
            ->keyBy('staff_id');

        $rows = $staff->map(function ($staff) use ($attendanceMap, $date) {
            $attendance = $attendanceMap->get($staff->id);

            return tap($attendance ?: new Attendance([
                'tenant_id' => $staff->tenant_id,
                'staff_id' => $staff->id,
                'attendance_date' => $date,
                'status' => 'absent',
                'source' => 'manual',
            ]), function ($row) use ($staff) {
                $row->setRelation('staff', $staff);
            });
        });

        $filename = 'attendance_' . $date . '.xlsx';

        return Excel::download(new HrAttendanceExport($rows), $filename);
    }

    public function import(Request $request, string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $path = $request->file('import_file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return back()->with('error', 'The file does not contain data rows.');
        }

        $headers = array_map(fn ($h) => strtolower(trim((string) $h)), array_values($rows[1]));
        $required = ['employee_id', 'attendance_date', 'status'];

        foreach ($required as $column) {
            if (!in_array($column, $headers, true)) {
                return back()->with('error', "Missing required column: {$column}");
            }
        }

        $headerIndex = array_flip($headers);
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue;
            }

            $employeeId = trim((string) ($row[array_search('employee_id', $headers)] ?? ''));
            $attendanceDate = trim((string) ($row[array_search('attendance_date', $headers)] ?? ''));
            $status = strtolower(trim((string) ($row[array_search('status', $headers)] ?? '')));

            if ($employeeId === '' && $attendanceDate === '' && $status === '') {
                $skipped++;
                continue;
            }

            $staff = Staff::where('tenant_id', $tenant->id)
                ->where('employee_id', $employeeId)
                ->first();

            if (! $staff) {
                $errors[] = "Row {$index}: invalid employee_id";
                continue;
            }

            if (!Carbon::hasFormat($attendanceDate, 'Y-m-d')) {
                $errors[] = "Row {$index}: attendance_date must be YYYY-MM-DD";
                continue;
            }

            if (!in_array($status, ['present', 'absent', 'half_day', 'leave', 'holiday', 'week_off'], true)) {
                $errors[] = "Row {$index}: invalid status";
                continue;
            }

            $data = [
                'tenant_id' => $tenant->id,
                'staff_id' => $staff->id,
                'attendance_date' => $attendanceDate,
                'check_in' => $this->valueFromRow($row, $headers, 'check_in') ?: null,
                'check_out' => $this->valueFromRow($row, $headers, 'check_out') ?: null,
                'working_minutes' => (int) ($this->valueFromRow($row, $headers, 'working_minutes') ?: 0),
                'is_late' => filter_var($this->valueFromRow($row, $headers, 'is_late') ?: false, FILTER_VALIDATE_BOOLEAN),
                'status' => $status,
                'source' => $this->valueFromRow($row, $headers, 'source') ?: 'manual',
                'remark' => $this->valueFromRow($row, $headers, 'remark') ?: null,
            ];

            Attendance::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'staff_id' => $data['staff_id'],
                    'attendance_date' => $data['attendance_date'],
                ],
                $data
            );

            $imported++;
        }

        $message = "Import complete: {$imported} rows saved.";
        if ($skipped) {
            $message .= " {$skipped} blank row(s) skipped.";
        }
        if ($errors) {
            $message .= " Some rows had errors.";
        }

        return back()->with($errors ? 'warning' : 'success', $message)->with('import_errors', $errors);
    }

    public function downloadTemplate(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $staff = Staff::where('tenant_id', $tenant->id)
            ->orderBy('employee_id')
            ->limit(5)
            ->get(['id', 'employee_id', 'first_name', 'last_name']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Attendance Import');

        $headers = [
            'employee_id',
            'attendance_date',
            'status',
            'check_in',
            'check_out',
            'working_minutes',
            'is_late',
            'source',
            'remark',
        ];

        foreach ($headers as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $header);
        }

        $sampleRows = $staff->map(function ($staff, $index) {
            return [
                $staff->employee_id,
                now()->toDateString(),
                $index === 0 ? 'present' : 'absent',
                $index === 0 ? '09:30' : '',
                $index === 0 ? '17:30' : '',
                $index === 0 ? 480 : 0,
                $index === 0 ? 'false' : 'false',
                'manual',
                $index === 0 ? "Sample row for {$staff->first_name} {$staff->last_name}" : 'Auto absent sample',
            ];
        })->values()->all();

        if (empty($sampleRows)) {
            $sampleRows = [
                ['EMP001', now()->toDateString(), 'present', '09:30', '17:30', 480, 'false', 'manual', 'Sample row'],
                ['EMP002', now()->toDateString(), 'absent', '', '', 0, 'false', 'manual', 'Auto absent sample'],
            ];
        }

        $rowNumber = 2;
        foreach ($sampleRows as $row) {
            foreach ($row as $colIndex => $value) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowNumber, $value);
            }
            $rowNumber++;
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->freezePane('A2');
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'attendance_import_template_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function valueFromRow(array $row, array $headers, string $key): ?string
    {
        if (!isset($headers[$key])) {
            return null;
        }

        return trim((string) ($row[array_search($key, $headers)] ?? ''));
    }

    private function resolveTenant(string $tenantKey): Tenant
    {
        if (preg_match('/-(\d+)(?:-|$)/', $tenantKey, $matches)) {
            return Tenant::where('status', 'active')->findOrFail((int) $matches[1]);
        }

        abort(404, 'Invalid tenant link');
    }
}
