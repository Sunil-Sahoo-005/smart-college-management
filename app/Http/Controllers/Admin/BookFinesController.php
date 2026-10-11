<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookFines;
use App\Models\BookIssues;
use App\Models\FeeCategory;
use App\Models\FeeInstallment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookFinesController extends Controller
{
    protected $bookFinesListRoute = 'admin.book_fines.index';

    // LIST
    public function index()
    {
        $bookFines = FeeInstallment::with(['student.libraryCard', 'feeCategory'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->whereHas('feeCategory', fn($q) => $q->where('name', 'Book Fine'))
            ->latest()
            ->paginate(10);

        $bookFinesJson = $bookFines->map(function ($fine) {
            return [
                'id'           => $fine->id,
                'student_name' => $fine->student->first_name . ' ' . ($fine->student->last_name ?? ''),
                // libraryCard relation se card_number (same as form.blade.php)
                'card_number'  => $fine->student->libraryCard->card_number ?? null,
                'amount'       => number_format($fine->amount, 2),
                'amount_raw'   => (float) $fine->amount,
                'reason'       => $fine->reason ?? $fine->description ?? 'Book Fine',
                'status'       => $fine->status,
                'due_date'     => $fine->due_date ? Carbon::parse($fine->due_date)->format('d M Y') : 'N/A',
                'edit_url'     => route('admin.book_fines.edit', $fine->id),
                'delete_url'   => route('admin.book_fines.destroy', $fine->id),
            ];
        });

        return view('Library.book_fines.index', compact('bookFines', 'bookFinesJson'));
    }

    // CREATE FORM
    public function create()
    {
        $students = Student::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('first_name')
            ->get();

        return view('Library.book_fines.form', [
            'bookFine' => null,
            'students' => $students
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount'     => 'required|numeric|min:0.01',
            'reason'     => 'nullable|string|max:500',
        ]);

        $bookFineCategory = FeeCategory::firstOrCreate(
            [
                'tenant_id' => auth()->user()->tenant_id,
                'name'      => 'Book Fine',
            ],
            [
                'description' => 'Auto created Book Fine category',
            ]
        );

        FeeInstallment::create([
            'tenant_id'        => auth()->user()->tenant_id,
            'student_id'       => $request->student_id,
            'fee_category_id'  => $bookFineCategory->id,
            'amount'           => $request->amount,
            'paid_amount'      => 0,
            'due_date'         => Carbon::now()->endOfMonth(),
            'status'           => 'unpaid',
            'reason'           => $request->reason ?: 'Book Fine',
            'description'      => $request->reason ?: 'Book Fine',
        ]);

        return redirect()->route('admin.book_fines.index')
            ->with('success', 'Book fine created successfully and added to student account!');
    }

    // EDIT FORM
    public function edit($id)
    {
        $bookFine = FeeInstallment::where('tenant_id', auth()->user()->tenant_id)
            ->whereHas('feeCategory', fn($q) => $q->where('name', 'Book Fine'))
            ->findOrFail($id);

        $students = Student::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('first_name')
            ->get();

        return view('Library.book_fines.form', compact('bookFine', 'students'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount'     => 'required|numeric|min:0.01',
            'reason'     => 'nullable|string|max:500',
        ]);

        $bookFine = FeeInstallment::where('tenant_id', auth()->user()->tenant_id)
            ->whereHas('feeCategory', fn($q) => $q->where('name', 'Book Fine'))
            ->findOrFail($id);

        $bookFine->update([
            'student_id'  => $request->student_id,
            'amount'      => $request->amount,
            'reason'      => $request->reason ?: 'Book Fine',
            'description' => $request->reason ?: 'Book Fine',
        ]);

        return redirect()->route($this->bookFinesListRoute)
            ->with('success', 'Book fine updated successfully!');
    }

    public function destroy($id)
    {
        $bookFine = FeeInstallment::where('tenant_id', auth()->user()->tenant_id)
            ->whereHas('feeCategory', fn($q) => $q->where('name', 'Book Fine'))
            ->findOrFail($id);

        $bookFine->delete();

        return redirect()->route($this->bookFinesListRoute)
            ->with('success', 'Book fine deleted successfully!');
    }
}