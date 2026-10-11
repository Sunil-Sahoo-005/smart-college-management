<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\Course;
use App\Models\CourseStream;
use App\Models\FeeInstallment;
use App\Models\FeeStructure;
use App\Models\Promotion;
use App\Models\ScholarshipTransaction;
use App\Models\Section;
use App\Models\Stream;
use App\Models\StudentClassEnrollment;
use App\Models\StudentScholarship;
use App\Models\StudentFeeStructure;
use App\Traits\HandlesFeeInstallments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    use HandlesFeeInstallments;
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $academicYears = AcademicYear::where('tenant_id', $tenantId)
            ->orderBy('name', 'desc')
            ->get(['id', 'name']);

        $courses = Course::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name', 'course_type']);

        return view('Academics.promotions.index', compact('academicYears', 'courses'));
    }

    public function getCourses(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $courses = Course::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name', 'course_type']);

        return response()->json([
            'courses' => $courses
        ]);
    }

    public function getStreams(Request $request)
    {
        $request->validate([
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
        ]);

        $tenantId = auth()->user()->tenant_id;

        $course = Course::with(['streams' => function ($q) use ($tenantId) {
            $q->where('streams.tenant_id', $tenantId)->orderBy('streams.name');
        }])->where('tenant_id', $tenantId)->findOrFail($request->course_id);

        $streams = $course->streams->map(function ($stream) {
            return [
                'id' => $stream->id,
                'name' => $stream->name,
            ];
        })->values();

        return response()->json([
            'streams' => $streams
        ]);
    }

    public function getClasses(Request $request)
    {
        $request->validate([
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'stream_id' => [
                'required',
                Rule::exists('streams', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
        ]);

        $tenantId = auth()->user()->tenant_id;

        $classes = Classe::where('tenant_id', $tenantId)
            ->whereHas('classStreamMappings.courseStream', function ($q) use ($request, $tenantId) {
                $q->where('tenant_id', $tenantId)
                    ->where('course_id', $request->course_id)
                    ->where('stream_id', $request->stream_id);
            })
            ->orderBy('order_index')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'classes' => $classes
        ]);
    }

    public function getSections(Request $request)
    {
        $request->validate([
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'stream_id' => [
                'required',
                Rule::exists('streams', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
        ]);

        $tenantId = auth()->user()->tenant_id;

        $sections = Section::where('tenant_id', $tenantId)
            ->where('class_id', $request->class_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'sections' => $sections
        ]);
    }

    public function getStudents(Request $request)
    {
        $request->validate([
            'academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'stream_id' => [
                'required',
                Rule::exists('streams', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
        ]);

        $tenantId = auth()->user()->tenant_id;

        $students = StudentClassEnrollment::with([
            'student:id,first_name,last_name,admission_number,roll_number',
            'academicYear:id,name',
            'course:id,name',
            'stream:id,name',
            'class:id,name',
            'section:id,name',
        ])
            ->where('tenant_id', $tenantId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('course_id', $request->course_id)
            ->where('stream_id', $request->stream_id)
            ->where('class_id', $request->class_id)
            ->where('status', 'active')
            ->when($request->section_id, function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            })
            ->orderBy('id')
            ->get();

        return response()->json([
            'students' => $students,
            'total' => $students->count(),
        ]);
    }

    public function getTargetStreams(Request $request)
    {
        return $this->getStreams($request);
    }

    public function getTargetClasses(Request $request)
    {
        return $this->getClasses($request);
    }

    public function getTargetSections(Request $request)
    {
        return $this->getSections($request);
    }

    public function promote(Request $request)
    {
        $request->validate([
            'from_academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'from_course_id' => [
                'required',
                Rule::exists('courses', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'from_stream_id' => [
                'required',
                Rule::exists('streams', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'from_class_id' => [
                'required',
                Rule::exists('classes', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'from_section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],

            'to_class_id' => [
                'required',
                Rule::exists('classes', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'to_section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where(function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],

            'promotions' => 'required|array|min:1',
            'promotions.*.status' => ['required', Rule::in(['promoted', 'repeated', 'failed'])],
            'promotions.*.student_gpa' => 'nullable|numeric|min:0|max:10',
            'promotions.*.remarks' => 'nullable|string|max:1000',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $successCount = 0;

        DB::transaction(function () use ($request, $tenantId, &$successCount) {
            foreach ($request->promotions as $studentId => $promotionData) {
                $currentEnrollment = StudentClassEnrollment::with([
                    'student',
                    'academicYear',
                    'course',
                    'stream',
                    'class',
                    'section'
                ])
                    ->where('tenant_id', $tenantId)
                    ->where('academic_year_id', $request->from_academic_year_id)
                    ->where('course_id', $request->from_course_id)
                    ->where('stream_id', $request->from_stream_id)
                    ->where('class_id', $request->from_class_id)
                    ->where('student_id', $studentId)
                    ->where('status', 'active')
                    ->when($request->from_section_id, function ($q) use ($request) {
                        $q->where('section_id', $request->from_section_id);
                    })
                    ->first();

                if (!$currentEnrollment) {
                    continue;
                }

                $newEnrollment = StudentClassEnrollment::updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'student_id' => $studentId,
                        'academic_year_id' => $request->from_academic_year_id,
                    ],
                    [
                        'course_id' => $request->from_course_id,
                        'stream_id' => $request->from_stream_id,
                        'class_id' => $request->to_class_id,
                        'section_id' => $request->to_section_id,
                        'status' => 'active',
                    ]
                );

                Promotion::create([
                    'tenant_id' => $tenantId,
                    'academic_year_id' => $request->from_academic_year_id,
                    'student_id' => $studentId,
                    'from_stream_name' => $currentEnrollment->stream?->name,
                    'from_class_name' => $currentEnrollment->class?->name,
                    'from_section_name' => $currentEnrollment->section?->name,
                    'to_stream_name' => $currentEnrollment->stream?->name,
                    'to_class_name' => Classe::find($request->to_class_id)?->name,
                    'to_section_name' => $request->to_section_id
                        ? Section::find($request->to_section_id)?->name
                        : null,
                    'student_gpa' => $promotionData['student_gpa'] ?? null,
                    'remarks' => $promotionData['remarks'] ?? null,
                    'status' => $promotionData['status'],
                ]);

                $currentEnrollment->update([
                    // 'status' => $promotionData['status'] === 'promoted' ? 'promoted' : $promotionData['status'],
                    'status' => 'active',
                ]);

                // Assign fee structure and generate installments based on course type
                if ($promotionData['status'] === 'promoted') {
                    $this->clearStudentScholarshipBalance($tenantId, $studentId);

                    $this->assignAndGeneratePromotedStudentFeeStructure(
                        $tenantId,
                        $studentId,
                        $request->from_academic_year_id,
                        $request->from_course_id,
                        $request->from_class_id,
                        $request->to_class_id,
                        $request->from_stream_id
                    );
                }

                $successCount++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$successCount} students.",
        ]);
    }
    private function generateInstallmentsForStudentFeeStructure(StudentFeeStructure $plan): int
    {
        $plan->load('feeStructure.items', 'academicYear');

        if (!$plan->feeStructure) {
            \Log::warning("Fee structure not found for installment generation", [
                'planId' => $plan->id,
                'studentId' => $plan->student_id
            ]);
            return 0;
        }

        if ($plan->feeStructure->items->isEmpty()) {
            \Log::warning("No fee structure items found", [
                'feeStructureId' => $plan->fee_structure_id,
                'studentId' => $plan->student_id,
                'planId' => $plan->id
            ]);
            return 0;
        }

        $start = Carbon::parse($plan->start_date);
        $end   = Carbon::parse($plan->end_date)->endOfDay();  // Convert to end of day for proper comparison

        \Log::info("Installment generation details", [
            'studentId' => $plan->student_id,
            'feeStructureId' => $plan->fee_structure_id,
            'planStart' => $start,
            'planEnd' => $end,
            'itemCount' => $plan->feeStructure->items->count()
        ]);

        $existingCount = FeeInstallment::where('tenant_id', $plan->tenant_id)
            ->where('student_id', $plan->student_id)
            ->whereBetween('due_date', [$start, $end])
            ->count();

        if ($existingCount > 0) {
            \Log::info("Installments already exist, skipping generation", [
                'studentId' => $plan->student_id,
                'existingCount' => $existingCount
            ]);
            $plan->update(['status' => 'generated']);
            return 0;
        }

        $createdCount = 0;
        $academicYearStart = Carbon::parse($plan->academicYear->start_date);

        foreach ($plan->feeStructure->items as $item) {
            // Calculate due date based on the academic year start, not the plan start date
            if ($item->due_month === null) {
                $due = $end->copy();
            } else {
                // Set due date to the specified month of the academic year
                $due = $academicYearStart->copy()->month($item->due_month)->endOfMonth();
            }

            \Log::debug("Processing fee item", [
                'itemId' => $item->id,
                'dueMonth' => $item->due_month,
                'calculatedDueDate' => $due,
                'planStart' => $start,
                'planEnd' => $end,
                'withinRange' => ($due >= $start && $due <= $end)
            ]);

            // Only create installment if due date falls within the plan's date range
            if ($due < $start || $due > $end) {
                \Log::debug("Due date outside plan range, skipping", [
                    'due' => $due->format('Y-m-d H:i:s'),
                    'start' => $start->format('Y-m-d H:i:s'),
                    'end' => $end->format('Y-m-d H:i:s')
                ]);
                continue;
            }

            FeeInstallment::create([
                'tenant_id'       => $plan->tenant_id,
                'student_id'      => $plan->student_id,
                'fee_category_id' => $item->fee_category_id,
                'amount'          => $item->amount,
                'due_date'        => $due,
                'status'          => 'unpaid',
            ]);

            $createdCount++;
            \Log::info("Installment created", [
                'studentId' => $plan->student_id,
                'dueDate' => $due,
                'amount' => $item->amount
            ]);
        }

        if ($createdCount > 0) {
            $plan->update(['status' => 'generated']);
        }

        \Log::info("Installment generation completed", [
            'studentId' => $plan->student_id,
            'createdCount' => $createdCount
        ]);

        return $createdCount;
    }

    private function clearStudentScholarshipBalance(int $tenantId, int $studentId): void
    {
    
        $scholarships = StudentScholarship::where('tenant_id', $tenantId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'approved'])
            ->where('balance_amount', '>', 0)
            ->orderBy('created_at')
            ->get();
// dd($scholarships);
        foreach ($scholarships as $studentScholarship) {
            $remainingBalance = (float) $studentScholarship->balance_amount;
            $usedAmount = (float) $studentScholarship->used_amount;
// dd($remainingBalance, $usedAmount);
            if ($remainingBalance <= 0) {
                continue;
            }

            $unpaidInstallments = FeeInstallment::where('tenant_id', $tenantId)
                ->where('student_id', $studentId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->orderBy('due_date')
                ->orderBy('id')
                ->get();
// dd($unpaidInstallments);
            foreach ($unpaidInstallments as $installment) {
                if ($remainingBalance <= 0) {
                    break;
                }

                $existingPaid = (float) ($installment->paid_amount ?? 0);
                $installmentDue = max(0, (float) $installment->amount - $existingPaid);

                if ($installmentDue <= 0) {
                    continue;
                }

                $amountUsed = min($remainingBalance, $installmentDue);
                $newPaidAmount = $existingPaid + $amountUsed;
                $newStatus = $newPaidAmount >= (float) $installment->amount ? 'paid' : 'partial';

                $installment->update([
                    'status' => $newStatus,
                    'paid_amount' => $newPaidAmount,
                ]);

                ScholarshipTransaction::create([
                    'student_scholarship_id' => $studentScholarship->id,
                    'installment_id' => $installment->id,
                    'amount_used' => $amountUsed,
                    'remarks' => 'Scholarship cleared during promotion',
                ]);
// dd($studentScholarship, $installment);
                $remainingBalance -= $amountUsed;
                $usedAmount += $amountUsed;
            }

            $studentScholarship->update([
                'used_amount' => $usedAmount,
                'balance_amount' => $remainingBalance,
                'status' => $remainingBalance > 0 ? 'approved' : 'completed',
            ]);
            // dd($studentScholarship);
        }
    }

    private function createFeeInstallmentsForPromotion(
        $tenantId,
        $studentId,
        $academicYearId,
        $streamId,
        $classId
    ) {
        $feeStructures = FeeStructure::where('tenant_id', $tenantId)
            // ->where('academic_year_id', $academicYearId)
            ->where('stream_id', $streamId)
            ->where('class_id', $classId)
            ->with('items')
            ->get();

        foreach ($feeStructures as $feeStructure) {
            foreach ($feeStructure->items as $item) {
                $dueDate = $this->calculateDueDate($academicYearId, $item->due_month);

                FeeInstallment::firstOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'student_id' => $studentId,
                        'fee_category_id' => $item->fee_category_id,
                        'due_date' => $dueDate,
                    ],
                    [
                        'amount' => $item->amount,
                        'status' => 'unpaid',
                    ]
                );
            }
        }
    }

    private function calculateDueDate($academicYearId, $dueMonth)
    {
        $academicYear = AcademicYear::findOrFail($academicYearId);
        $yearStart = Carbon::parse($academicYear->start_date);

        if ($dueMonth) {
            return $yearStart->copy()->month($dueMonth)->day(1);
        }

        return Carbon::parse($academicYear->end_date)->endOfDay();
    }

    /**
     * Assign fee structure to promoted student and generate installments
     * based on course type (yearly or semester)
     */
    private function assignAndGeneratePromotedStudentFeeStructure(
        $tenantId,
        $studentId,
        $academicYearId,
        $courseId,
        $fromClassId,
        $toClassId,
        $streamId
    ): void {
        $course = Course::where('tenant_id', $tenantId)->find($courseId);
        $toClass = Classe::where('tenant_id', $tenantId)->find($toClassId);
        $academicYear = AcademicYear::where('tenant_id', $tenantId)->find($academicYearId);

        if (!$course || !$toClass || !$academicYear) {
            return;
        }

        // Determine the target year number based on course type
        // Semester: Sem 1-2 = Year 1, Sem 3-4 = Year 2, etc.
        // Yearly: Class 1 = Year 1, Class 2 = Year 2, etc.
        if ($course->course_type === 'semester') {
             $targetYear = (int) ceil($toClass->order_index / 2);
        } else {
             $targetYear = (int) $toClass->order_index;
        }

        // Find all specifically matching fee structures for this Year + Stream
        $feeStructures = FeeStructure::where('tenant_id', $tenantId)
            ->where('course_id', $courseId)
            ->where('stream_id', $streamId)
            ->where('year_number', $targetYear)
            ->get();

        if ($feeStructures->isEmpty()) {
            \Log::warning("No fee structure found for promotion", [
                'studentId' => $studentId,
                'year' => $targetYear,
                'courseId' => $courseId,
                'streamId' => $streamId
            ]);
            return;
        }

        foreach ($feeStructures as $feeStructure) {
            // Calculate dates for this specific structure
            $yearStart = Carbon::parse($academicYear->start_date);
            
            if ($course->course_type === 'semester') {
                $structureStartDate = $yearStart->copy(); 
                $structureEndDate = Carbon::parse($academicYear->end_date);
            } else {
                $structureStartDate = $yearStart->copy();
                $structureEndDate = Carbon::parse($academicYear->end_date);
            }

            // find or create the assignment for this specific structure
            $plan = StudentFeeStructure::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'student_id' => $studentId,
                    'fee_structure_id' => $feeStructure->id,
                    'academic_year_id' => $academicYearId,
                ],
                [
                    'start_date' => $structureStartDate,
                    'end_date' => $structureEndDate,
                    'status' => 'assigned', 
                ]
            );

            // Generate installments using the shared robust logic
            $this->generateInstallments($plan);
        }
    }

    // Removed buggy redundant methods
}
