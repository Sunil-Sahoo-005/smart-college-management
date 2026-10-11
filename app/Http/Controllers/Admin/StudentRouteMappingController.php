<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Routes;
use App\Models\RouteStop;
use App\Models\StudentRouteMapping;
use App\Models\FeeCategory;
use App\Models\FeeInstallment;
use Carbon\Carbon;

class StudentRouteMappingController extends Controller
{
    protected $studentroutemappingListRoute = 'admin.studentroutemapping.index';

    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $query = StudentRouteMapping::with(['student', 'stop', 'route']);
        } else {
            $query = StudentRouteMapping::with(['student', 'stop', 'route'])
                ->where('tenant_id', $user->tenant_id);
        }

        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $request->search . '%']);
            });
        }

        if ($request->filled('stop')) {
            $query->whereHas('stop', function ($q) use ($request) {
                $q->where('stop_name', 'like', '%' . $request->stop . '%');
            });
        }

        $studentroutemapping = $query->orderBy('student_id')->paginate(10)->withQueryString();

        return view('Travels.studentroutemapping.index', compact('studentroutemapping'));
    }

    public function create()
    {
        $user = auth()->user();
        $studentroutemapping = null;

        $assignedStudentIds = StudentRouteMapping::where('tenant_id', $user->tenant_id)
            ->pluck('student_id');

        $students = Student::where('tenant_id', $user->tenant_id)
            ->whereNotIn('id', $assignedStudentIds)
            ->select('id', 'first_name', 'last_name', 'admission_number')
            ->get();

        $allStops = RouteStop::whereHas('route', function ($q) use ($user) {
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', $user->tenant_id);
            })
            ->get()
            ->groupBy(function ($stop) {
                return strtolower(str_replace(' ', '', $stop->stop_name));
            })
            ->map(function ($group) {
                return $group->first();
            })
            ->values();

        return view('Travels.studentroutemapping.form', compact('studentroutemapping', 'students', 'allStops'));
    }

    public function edit(StudentRouteMapping $studentroutemapping)
    {
        $this->authorizeTenant($studentroutemapping);

        $user = auth()->user();

        $assignedStudentIds = StudentRouteMapping::where('tenant_id', $user->tenant_id)
            ->where('id', '!=', $studentroutemapping->id)
            ->pluck('student_id');

        $students = Student::where('tenant_id', $user->tenant_id)
            ->whereNotIn('id', $assignedStudentIds)
            ->select('id', 'first_name', 'last_name', 'admission_number')
            ->get();

        $allStops = RouteStop::whereHas('route', function ($q) use ($user) {
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', $user->tenant_id);
            })
            ->get()
            ->groupBy(function ($stop) {
                return strtolower(str_replace(' ', '', $stop->stop_name));
            })
            ->map(function ($group) {
                return $group->first();
            })
            ->values();

        return view('Travels.studentroutemapping.form', compact('studentroutemapping', 'students', 'allStops'));
    }

    public function findStudentByRegNumber(Request $request)
    {
        $student = Student::where('admission_number', $request->reg_number)
                    ->with([
                        'tenant',
                        'activeEnrollment.academicYear',
                        'activeEnrollment.course',
                        'activeEnrollment.stream',
                        'activeEnrollment.section',
                        'activeEnrollment.classStream',
                    ])
                    ->select(
                        'id', 'first_name', 'last_name', 'admission_number',
                        'tenant_id', 'gender', 'dob', 'blood_group',
                        'category', 'email', 'status', 'religion', 'nationality'
                    )
                    ->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $e = $student->activeEnrollment;

        return response()->json([
            'id'               => $student->id,
            'first_name'       => $student->first_name,
            'last_name'        => $student->last_name,
            'admission_number' => $student->admission_number,
            'gender'           => $student->gender,
            'dob'              => $student->dob?->format('d M Y'),
            'blood_group'      => $student->blood_group,
            'category'         => $student->category,
            'email'            => $student->email,
            'status'           => $student->status,
            'college_name'     => $student->tenant?->name ?? null,
            'academic_year'    => $e?->academicYear?->name ?? $e?->academicYear?->year_name ?? null,
            'course'           => $e?->course?->name ?? $e?->course?->course_name ?? null,
            'stream'           => $e?->stream?->name ?? $e?->stream?->stream_name ?? null,
            'section'          => $e?->section?->name ?? $e?->section?->section_name ?? null,
            'class_stream'     => $e?->classStream?->name ?? $e?->classStream?->class_stream_name ?? null,
            'student_type'     => $e?->student_type_label ?? null,
            'residence_type'   => $e?->residence_type_label ?? null,
        ]);
    }

    public function store(Request $request)
    {
        //  dd($request->all());
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'stop_id'       => 'required|exists:route_stops,id',
            'route_id'      => 'required|exists:routes,id',
            'transport_fee' => 'nullable|numeric|min:0',
        ]);

        $isUpdate = !empty($request->get('id'));
        //   dd($isUpdate); 

        if ($isUpdate) {
            // ── UPDATE ──────────────────────────────────────────
            $studentroutemapping = StudentRouteMapping::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($studentroutemapping)) {
                abort(404);
            }

            $studentroutemapping->student_id    = $request->get('student_id');
            $studentroutemapping->stop_id       = $request->get('stop_id');
            $studentroutemapping->route_id      = $request->get('route_id');
            $studentroutemapping->transport_fee = $request->get('transport_fee');

            try {
                $studentroutemapping->save();
                //  dd('mapping saved, fee check: ' . ($request->filled('transport_fee') ? 'filled' : 'not filled') . ' | value: ' . $request->transport_fee . ' | greater than 0: ' . ($request->transport_fee > 0 ? 'yes' : 'no'));

                // ✅ FeeInstallment update karo agar exist karta hai
                if ($request->filled('transport_fee') && $request->transport_fee > 0) {
                    $stop = RouteStop::find($request->stop_id);

                    $existingFee = FeeInstallment::where('tenant_id', auth()->user()->tenant_id)
                        ->where('student_id', $request->student_id)
                        ->whereHas('feeCategory', fn($q) => $q->where('name', 'Transport Fee'))
                        ->latest()
                        ->first();

                    if ($existingFee) {
                        $existingFee->update([
                            'amount'      => $request->transport_fee,
                            'due_date'    => Carbon::now()->endOfMonth(),
                            'reason'      => 'Transport Fee - Stop: ' . ($stop->stop_name ?? 'N/A'),
                            'description' => 'Transport Fee - Stop: ' . ($stop->stop_name ?? 'N/A'),
                        ]);
                    }
                }

                return redirect()->back()->with('success', 'Mapping updated successfully');
            } catch (\Exception $e) {
                //  dd('ERROR: ' . $e->getMessage());
                \Log::error('StudentRouteMapping update error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
            }

        } else {
            // ── CREATE ──────────────────────────────────────────
            $studentroutemapping = new StudentRouteMapping;
            $studentroutemapping->tenant_id     = auth()->user()->tenant_id;
            $studentroutemapping->student_id    = $request->get('student_id');
            $studentroutemapping->stop_id       = $request->get('stop_id');
            $studentroutemapping->route_id      = $request->get('route_id');
            $studentroutemapping->transport_fee = $request->get('transport_fee');

            try {
                $studentroutemapping->save();

                // ✅ FeeInstallment create karo (sirf agar transport_fee filled ho)
                if ($request->filled('transport_fee') && $request->transport_fee > 0) {
                    $stop = RouteStop::find($request->stop_id);

                    $transportFeeCategory = FeeCategory::firstOrCreate(
                        [
                            'tenant_id' => auth()->user()->tenant_id,
                            'name'      => 'Transport Fee',
                        ],
                        [
                            'description' => 'Auto created Transport Fee category',
                        ]
                    );

                    FeeInstallment::create([
                        'tenant_id'       => auth()->user()->tenant_id,
                        'student_id'      => $request->student_id,
                        'fee_category_id' => $transportFeeCategory->id,
                        'amount'          => $request->transport_fee,
                        'due_date'        => Carbon::now()->endOfMonth(),
                        'status'          => 'unpaid',
                        'reason'          => 'Transport Fee - Stop: ' . ($stop->stop_name ?? 'N/A'),
                        'description'     => 'Transport Fee - Stop: ' . ($stop->stop_name ?? 'N/A'),
                    ]);
                }

                return redirect()->route($this->studentroutemappingListRoute)
                    ->with('success', 'Mapping created successfully');
            } catch (\Exception $e) {
                \Log::error('StudentRouteMapping save error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
            }
        }
    }

    public function destroy(StudentRouteMapping $studentroutemapping)
    {
        $this->authorizeTenant($studentroutemapping);

        try {
            // ✅ FeeInstallment bhi delete karo
            FeeInstallment::where('tenant_id', auth()->user()->tenant_id)
                ->where('student_id', $studentroutemapping->student_id)
                ->whereHas('feeCategory', fn($q) => $q->where('name', 'Transport Fee'))
                ->latest()
                ->first()
                ?->delete();

            $studentroutemapping->delete();

            return back()->with('success', 'Mapping deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete mapping, it may be in use.');
        }
    }

    public function show($id)
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $mapping = StudentRouteMapping::where('id', $id)
                ->with(['student', 'stop', 'route', 'route.vehicle', 'route.stops'])
                ->firstOrFail();
        } else {
            $mapping = StudentRouteMapping::where('id', $id)
                ->where('tenant_id', $user->tenant_id)
                ->with(['student', 'stop', 'route', 'route.vehicle', 'route.stops'])
                ->firstOrFail();
        }

        $allRoutesForStop = collect();

        if ($mapping->stop) {
            $normalizedName = strtolower(str_replace(' ', '', $mapping->stop->stop_name));

            $allRoutesForStop = RouteStop::whereHas('route', function ($q) use ($user) {
                    $q->whereNull('tenant_id')
                      ->orWhere('tenant_id', $user->tenant_id);
                })
                ->with(['route' => function ($q) {
                    $q->with(['vehicle', 'stops']);
                }])
                ->get()
                ->filter(function ($rs) use ($normalizedName) {
                    return strtolower(str_replace(' ', '', $rs->stop_name)) === $normalizedName;
                })
                ->map(fn($rs) => $rs->route)
                ->filter()
                ->unique('id')
                ->values();
        }

        $routesMapData = $allRoutesForStop->map(function ($route) {
            return [
                'id'        => $route->id,
                'name'      => $route->name,
                'start_lat' => $route->start_lat ?? null,
                'start_lng' => $route->start_lng ?? null,
                'end_lat'   => $route->end_lat   ?? null,
                'end_lng'   => $route->end_lng   ?? null,
                'stops'     => $route->stops
                    ? $route->stops->sortBy('order')->map(fn($s) => [
                        'id'      => $s->id,
                        'name'    => $s->stop_name,
                        'lat'     => (float) $s->lat,
                        'lng'     => (float) $s->lng,
                        'order'   => $s->order,
                        'arrival' => $s->arrival_time,
                    ])->values()
                    : collect([]),
            ];
        })->values();

        $routeStopsJson = $mapping->route && $mapping->route->stops
            ? $mapping->route->stops->sortBy('order')->map(function ($s) {
                return [
                    'id'      => $s->id,
                    'name'    => $s->stop_name,
                    'lat'     => (float) $s->lat,
                    'lng'     => (float) $s->lng,
                    'order'   => $s->order,
                    'arrival' => $s->arrival_time,
                ];
            })->values()
            : collect([]);

        return view('Travels.studentroutemapping.show', compact(
            'mapping',
            'routeStopsJson',
            'allRoutesForStop',
            'routesMapData'
        ));
    }

    protected function authorizeTenant(StudentRouteMapping $studentroutemapping)
    {
        if ($studentroutemapping->tenant_id !== null &&
            $studentroutemapping->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }

    public function getStops($route_id)
    {
        $stops = RouteStop::where('route_id', $route_id)->get();
        return response()->json($stops);
    }

    public function getBusesByStop($stop_id)
    {
        $user = auth()->user();
        $selectedStop = RouteStop::find($stop_id);

        if (!$selectedStop) {
            return response()->json([]);
        }

        $normalizedName = strtolower(str_replace(' ', '', $selectedStop->stop_name));

        $allStops = RouteStop::whereHas('route', function ($q) use ($user) {
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', $user->tenant_id);
            })
            ->with(['route.vehicle'])
            ->get()
            ->filter(function ($stop) use ($normalizedName) {
                return strtolower(str_replace(' ', '', $stop->stop_name)) === $normalizedName;
            });

        $buses = $allStops
            ->filter(fn($rs) => $rs->route && $rs->route->vehicle)
            ->map(fn($rs) => [
                'route_id'            => $rs->route->id,
                'route_name'          => $rs->route->name,
                'vehicle_id'          => $rs->route->vehicle->id,
                'vehicle_type'        => $rs->route->vehicle->type,
                'registration_number' => $rs->route->vehicle->registration_number,
            ])
            ->unique('route_id')
            ->values();

        return response()->json($buses);
    }
}