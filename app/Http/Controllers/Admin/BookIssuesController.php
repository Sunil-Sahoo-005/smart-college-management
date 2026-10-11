<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookIssues;
use App\Models\Books;
use App\Models\LibraryCards;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookIssuesController extends Controller
{
    protected $bookIssuesListRoute = 'admin.book_issues.index';

    /**
     * List all book issues with search + filter
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Get categories for filter dropdown
        $categories = Books::where('tenant_id', auth()->user()->tenant_id)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $query = BookIssues::with(['book', 'student', 'staff'])
            ->where('tenant_id', auth()->user()->tenant_id);

        // Search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('book', function ($bookQuery) use ($searchTerm) {
                    $bookQuery->where('title', 'LIKE', "%{$searchTerm}%")
                              ->orWhere('isbn', 'LIKE', "%{$searchTerm}%")
                              ->orWhere('author', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhereHas('student', function ($studentQuery) use ($searchTerm) {
                    $studentQuery->where('first_name', 'LIKE', "%{$searchTerm}%")
                                 ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                                 ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                                 ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
                })
                ->orWhereHas('staff', function ($staffQuery) use ($searchTerm) {
                    $staffQuery->where('first_name', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                               ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
                });
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('book', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Status filter
        if ($request->filled('book_status')) {
            $query->where('status', $request->book_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $query->where('issue_date', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->where('issue_date', '<=', $dateTo);
        }

        $book_issues = $query->latest()
            ->paginate(10)
            ->appends($request->query());

        // Stats
        $totalBooks   = Books::where('tenant_id', auth()->user()->tenant_id)->count();
        $totalIssued  = BookIssues::where('tenant_id', auth()->user()->tenant_id)->where('status', 'issued')->count();
        $totalReturned = BookIssues::where('tenant_id', auth()->user()->tenant_id)->where('status', 'returned')->count();
        $totalDamaged  = Books::where('tenant_id', auth()->user()->tenant_id)->where('status', 'damaged')->count();

        return view('Library.book_issues.index', compact(
            'book_issues',
            'search',
            'categories',
            'totalBooks',
            'totalIssued',
            'totalReturned',
            'totalDamaged'
        ));
    }

    /**
     * Create form
     */
    public function create()
    {
        $staffs = Staff::where('tenant_id', auth()->user()->tenant_id)->get();
        $availableBooks = Books::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'available')
            ->get()
            ->groupBy('isbn');

        return view('Library.book_issues.form', compact('staffs', 'availableBooks'));
    }

    /**
     * Search student by library card number
     */
    public function searchStudent(Request $request)
    {
        $request->validate(['card_number' => 'required|string|max:50']);

        $student = Student::whereHas('libraryCard', function ($q) use ($request) {
            $q->where('card_number', $request->card_number)
              ->where('tenant_id', auth()->user()->tenant_id);
        })
            ->with('libraryCard')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if ($student) {
            return response()->json([
                'success' => true,
                'student' => [
                    'id'          => $student->id,
                    'name'        => $student->first_name . ' ' . $student->last_name,
                    'email'       => $student->email ?? 'N/A',
                    'card_number' => $student->libraryCard->card_number,
                    'expiry_date' => $student->libraryCard->expiry_date?->format('d-M-Y'),
                    'is_valid'    => $student->libraryCard->expiry_date
                        ? Carbon::parse($student->libraryCard->expiry_date)->isFuture()
                        : true,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Card not found or expired']);
    }

    /**
     * Search available books
     */
 public function searchBooks(Request $request)
{
    $request->validate(['q' => 'nullable|string|max:100']);

    $books = Books::where('tenant_id', auth()->user()->tenant_id)
        ->where('status', 'available')
        ->with(['room', 'self', 'row', 'academicGroup'])   // ← self aur row add
        ->when($request->q, function ($q, $search) {
            $terms = array_filter(explode(' ', $search));
            foreach ($terms as $term) {
                $q->where(function ($subQ) use ($term) {
                    $subQ->where('title', 'LIKE', "%{$term}%")
                         ->orWhere('author', 'LIKE', "%{$term}%")
                         ->orWhere('isbn', 'LIKE', "%{$term}%")
                         ->orWhere('category', 'LIKE', "%{$term}%");
                });
            }
        })
        ->orderBy('title')
        ->limit(12)
        ->get()
        ->map(function ($book) {
            return array_merge($book->toArray(), [
                'room_name'           => $book->room?->name          ?? null,
                'self_name'           => $book->self?->name          ?? null,  // ← add
                'row_name'            => $book->row?->name           ?? null,  // ← add
                'academic_group_name' => $book->academicGroup?->name ?? null,
            ]);
        });

    return response()->json($books);
}

    /**
     * Store new book issue
     * ✅ Uses explicit issue_date + due_date instead of duration days
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'  => 'nullable|exists:students,id',
            'staff_id'    => 'nullable|exists:staff,id',
            'books'       => 'required|json|min:3|max:5000',
            'issue_date'  => 'required|date|before_or_equal:today',
            'due_date'    => 'required|date|after:issue_date',
        ]);

        if (!$request->student_id && !$request->staff_id) {
            return back()->withErrors(['student_id' => 'Please select a student or staff member.']);
        }

        if ($request->student_id && $request->staff_id) {
            return back()->withErrors(['student_id' => 'Please select either student OR staff, not both.']);
        }

        $booksData = json_decode($request->books, true);
        if (!$booksData || !is_array($booksData) || count($booksData) === 0) {
            return back()->withErrors(['books' => 'No valid books selected.']);
        }

        $batchId      = 'ISSUE-' . Str::uuid();
        $issueDate    = Carbon::parse($request->issue_date);
        $dueDate      = Carbon::parse($request->due_date);
        $successCount = 0;

        DB::transaction(function () use ($booksData, $request, $batchId, $issueDate, $dueDate, &$successCount) {
            foreach ($booksData as $bookData) {
                if (!isset($bookData['book_id']) || !is_numeric($bookData['book_id'])) {
                    continue;
                }

                $book = Books::where('tenant_id', auth()->user()->tenant_id)
                    ->where('id', $bookData['book_id'])
                    ->where('status', 'available')
                    ->first();

                if (!$book) {
                    \Log::warning("Book issue failed: Book ID {$bookData['book_id']} not available");
                    continue;
                }

                BookIssues::create([
                    'tenant_id'       => auth()->user()->tenant_id,
                    'issue_batch_id'  => $batchId,
                    'book_id'         => $book->id,
                    'student_id'      => $request->student_id,
                    'staff_id'        => $request->staff_id,
                    'library_card_id' => $request->student_id
                        ? LibraryCards::where('student_id', $request->student_id)->first()?->id
                        : null,
                    'issue_date'      => $issueDate,
                    'due_date'        => $dueDate,
                    'status'          => 'issued',
                    'fine_amount'     => 0,
                ]);

                $book->update(['status' => 'issued']);
                $successCount++;
            }
        });

        if ($successCount > 0) {
            session()->forget('selected_books');
            return redirect()
                ->route('admin.book_issues.index')
                ->with('success', "✅ Successfully issued {$successCount} book(s)! Batch: {$batchId}");
        }

        return back()->withErrors(['books' => 'No books could be issued.']);
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $book_issue = BookIssues::where('tenant_id', auth()->user()->tenant_id)
            ->with(['book', 'student', 'staff'])
            ->findOrFail($id);

        $books    = Books::where('tenant_id', auth()->user()->tenant_id)->get();
        $students = Student::where('tenant_id', auth()->user()->tenant_id)->get();
        $staffs   = Staff::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('Library.book_issues.form', compact('book_issue', 'books', 'students', 'staffs'));
    }

    /**
     * Update book issue
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'book_id'     => 'required|exists:books,id',
            'student_id'  => 'nullable|exists:students,id',
            'staff_id'    => 'nullable|exists:staff,id',
            'issue_date'  => 'required|date',
            'due_date'    => 'required|date|after_or_equal:issue_date',
            'return_date' => 'nullable|date|after_or_equal:issue_date',
            'fine_amount' => 'nullable|numeric|min:0',
            'status'      => 'required|in:issued,returned,overdue',
        ]);

        $book_issue = BookIssues::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        $updateData = [
            'book_id'     => $request->book_id,
            'issue_date'  => $request->issue_date,
            'due_date'    => $request->due_date,
            'return_date' => $request->return_date,
            'fine_amount' => $request->fine_amount ?? 0,
            'status'      => $request->status,
        ];

        if ($request->filled('student_id')) {
            $updateData['student_id'] = $request->student_id;
            $updateData['staff_id']   = null;
            $libraryCard = LibraryCards::where('student_id', $request->student_id)->first();
            $updateData['library_card_id'] = $libraryCard ? $libraryCard->id : null;
        } elseif ($request->filled('staff_id')) {
            $updateData['staff_id']        = $request->staff_id;
            $updateData['student_id']      = null;
            $updateData['library_card_id'] = null;
        }

        $book_issue->update($updateData);

        if ($request->status === 'returned' && $book_issue->book) {
            $book_issue->book->update(['status' => 'available']);
        } elseif ($request->status === 'issued' && $book_issue->book) {
            $book_issue->book->update(['status' => 'issued']);
        }

        return redirect()
            ->route('admin.book_issues.index')
            ->with('success', 'Book Issue updated successfully');
    }

    /**
     * Delete book issue
     */
    public function destroy($id)
    {
        BookIssues::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id)
            ->delete();

        return redirect()
            ->route($this->bookIssuesListRoute)
            ->with('success', 'Book Issue deleted successfully');
    }

    // =====================
    // RETURN BOOKS
    // =====================

    public function returnIndex()
    {
        $staffs = Staff::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('Library.book_issues.return_form', compact('staffs'));
    }

    public function returnSearchStudent(Request $request)
    {
        return $this->searchStudent($request);
    }

    public function returnSearchBooks(Request $request)
    {
        $studentId = $request->input('student_id');
        $staffId   = $request->input('staff_id');

        if (!$studentId && !$staffId) {
            return response()->json(['error' => 'No borrower selected']);
        }

        $issues = BookIssues::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'issued')
            ->when($studentId, fn($q) => $q->where('student_id', $studentId))
            ->when($staffId,   fn($q) => $q->where('staff_id', $staffId))
            ->with(['book'])
            ->latest('issue_date')
            ->limit(20)
            ->get();

        return response()->json($issues->map(function ($issue) {
            return [
                'id'           => $issue->book_id,
                'title'        => $issue->book->title,
                'author'       => $issue->book->author ?? 'Unknown',
                'isbn'         => $issue->book->isbn,
                'copy_number'  => $issue->book->copy_number,
                'copy_isbn'    => $issue->book->copy_isbn,
                'issue_date'   => $issue->issue_date->format('M d'),
                'due_date'     => $issue->due_date->format('M d'),
                'overdue_days' => now()->diffInDays($issue->due_date, false),
                'fine_amount'  => 0,
                'book_image'   => $issue->book->book_image
                    ? Storage::url($issue->book->book_image)
                    : null,
            ];
        }));
    }

    public function returnStore(Request $request)
    {
        $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'staff_id'   => 'nullable|exists:staff,id',
            'books'      => 'required|json|min:3|max:5000',
        ]);

        if (!$request->student_id && !$request->staff_id) {
            return back()->withErrors(['student_id' => 'Please select a borrower']);
        }

        $booksData = json_decode($request->books, true);
        if (!is_array($booksData) || empty($booksData)) {
            return back()->withErrors(['books' => 'No books selected']);
        }

        $successCount = 0;

        DB::transaction(function () use ($booksData, $request, &$successCount) {
            foreach ($booksData as $bookData) {
                $book = Books::where('tenant_id', auth()->user()->tenant_id)
                    ->where('id', $bookData['book_id'])
                    ->where('status', 'issued')
                    ->first();

                if (!$book) continue;

                $issue = BookIssues::where('book_id', $book->id)
                    ->where('status', 'issued')
                    ->latest('issue_date')
                    ->first();

                if ($issue) {
                    $issue->update([
                        'status'      => 'returned',
                        'return_date' => now(),
                        'fine_amount' => 0,
                    ]);
                    $book->update(['status' => 'available']);
                    $successCount++;
                }
            }
        });

        return redirect()
            ->route('admin.book_issues.index')
            ->with('success', "✅ Successfully returned {$successCount} book(s)! No fines applied.");
    }
}