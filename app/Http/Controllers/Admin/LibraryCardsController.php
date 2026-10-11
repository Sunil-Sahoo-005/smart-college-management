<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryCards;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class LibraryCardsController extends Controller
{
    protected $libraryCardsListRoute = 'admin.library_cards.index';
    
    public function index(Request $request)  // Add Request parameter
    {
        $search = $request->input('search');
        
        $query = LibraryCards::where('tenant_id', auth()->user()->tenant_id)
            ->with('student'); // Eager load student relationship

        // Search filter - search by student name or card number
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                // Search by card number
                $q->where('card_number', 'LIKE', "%{$searchTerm}%")
                  // Search by student name
                  ->orWhereHas('student', function ($studentQuery) use ($searchTerm) {
                      $studentQuery->where('first_name', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter (by issue_date)
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $query->where('issue_date', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->where('issue_date', '<=', $dateTo);
        }

        $library_cards = $query->orderBy('card_number')
            ->paginate(10)
            ->appends($request->query());

        // Calculate stats
        $totalCards = LibraryCards::where('tenant_id', auth()->user()->tenant_id)->count();
        $activeCards = LibraryCards::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'active')
            ->count();
        $suspendedCards = LibraryCards::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'suspended')
            ->count();
        $expiredCards = LibraryCards::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'expired')
            ->count();

        return view('library.library_cards.index', compact(
            'library_cards', 
            'search',
            'totalCards',
            'activeCards',
            'suspendedCards',
            'expiredCards'
        ));
    }
    
    public function create()
    {
        $library_cards = null;
        $students = Student::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('library.library_cards.form', compact('library_cards', 'students'));
    }
    
    public function edit($id)
    {
        $library_cards = LibraryCards::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with('student')
            ->first();

        if (empty($library_cards)) {
            abort(404);
        }
        $students = Student::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('library.library_cards.form', compact('library_cards', 'students'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id'   => 'required|exists:students,id',
            'card_number'  => 'required|string|max:255|unique:library_cards,card_number,' . $request->get('id'),
            'issue_date'   => 'required|date',
            'card_serial_number' => 'nullable|string|max:255|unique:library_cards,card_serial_number,' . $request->get('id'), 
            'expiry_date'  => 'nullable|date|after:issue_date',
            'status'       => 'required|in:active,suspended,expired',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!empty($request->get('id'))) {
            $library_cards = LibraryCards::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($library_cards)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->route($this->libraryCardsListRoute),
                'msg' => 'Library card updated successfully',
            ];
        } else {
            $library_cards = new LibraryCards;
            $library_cards->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->libraryCardsListRoute),
                'msg' => 'Library card created successfully',
            ];
        }

        $library_cards->student_id   = $request->get('student_id');
        $library_cards->card_number  = $request->get('card_number');
        $library_cards->card_serial_number = $request->get('card_serial_number'); 
        $library_cards->issue_date   = $request->get('issue_date');
        $library_cards->expiry_date  = $request->get('expiry_date');
        $library_cards->status       = $request->get('status');

        try {
            $library_cards->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        $library_cards = LibraryCards::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (empty($library_cards)) {
            abort(404);
        }

        try {
            $library_cards->delete();
            return redirect()->back()->with('success', 'Library card deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    /**
     * Generate unique card number
     */
    public function generateCardNumber()
    {
        $tenantId = auth()->user()->tenant_id;
        $prefix = 'LIB-' . $tenantId . '-';
        
        $lastCard = LibraryCards::where('tenant_id', $tenantId)
            ->where('card_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastCard) {
            $lastNumber = (int) substr($lastCard->card_number, strlen($prefix));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }
        
        $cardNumber = $prefix . $newNumber;
        
        return response()->json(['card_number' => $cardNumber]);
    }
}