<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Books;
use App\Models\LibraryMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StockEntryController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
 
        $query = Books::where('tenant_id', $tenantId)
            ->whereNotNull('order_id')          // ← only stock-entry books
            ->select([
                'order_id',
                'bill_no',
                'bill_date',
                'withdrawal_no',
                'withdrawal_date',
                'status',
                'source',
                'remarks',
                'bill_pdf',
                DB::raw('MIN(created_at) as created_at'),
                DB::raw('COUNT(*) as total_copies'),
                DB::raw('COUNT(DISTINCT CONCAT(title, "|", COALESCE(author,""), "|", COALESCE(publisher,""), "|", COALESCE(edition,""))) as total_titles'),
                DB::raw('SUM(price) as total_amount'),
            ])
            ->groupBy([
                'order_id',
                'bill_no',
                'bill_date',
                'withdrawal_no',
                'withdrawal_date',
                'status',
                'source',
                'remarks',
                'bill_pdf',
            ]);
 
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'LIKE', "%{$search}%")
                  ->orWhere('bill_no',  'LIKE', "%{$search}%")
                  ->orWhere('source',   'LIKE', "%{$search}%");
            });
        }
 
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
 
        if ($request->filled('date_from')) {
            $query->whereDate('bill_date', '>=', $request->date_from);
        }
 
        if ($request->filled('date_to')) {
            $query->whereDate('bill_date', '<=', $request->date_to);
        }
 
        $entries = $query->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->query());
 
        $statuses = ['received', 'pending', 'returned', 'cancelled'];
 
        return view('Library.stock.index', compact('entries', 'statuses'));
    }
 
    /**
     * SHOW — Detail view for one stock entry (by order_id).
     * Shows the bill/withdrawal header + every book copy in that order.
     */
    public function show($orderId)
    {
        $tenantId = auth()->user()->tenant_id;
 
        // Verify this order belongs to this tenant
        $firstBook = Books::where('tenant_id', $tenantId)
            ->where('order_id', $orderId)
            ->firstOrFail();
 
        // Header info (all same for every book in the order)
        $header = (object) [
            'order_id'        => $firstBook->order_id,
            'bill_no'         => $firstBook->bill_no,
            'bill_date'       => $firstBook->bill_date,
            'withdrawal_no'   => $firstBook->withdrawal_no,
            'withdrawal_date' => $firstBook->withdrawal_date,
            'status'          => $firstBook->status,
            'source'          => $firstBook->source,
            'remarks'         => $firstBook->remarks,
            'bill_pdf'        => $firstBook->bill_pdf,
        ];
 
        // All book copies in this order
        $copies = Books::where('tenant_id', $tenantId)
            ->where('order_id', $orderId)
            ->orderBy('title')
            ->orderBy('copy_number')
            ->get();
 
        // Group copies by unique title+author+publisher+edition to show title-level summaries
        $titleGroups = $copies->groupBy(function ($book) {
            return $book->title . '|' . ($book->author ?? '') . '|' . ($book->publisher ?? '') . '|' . ($book->edition ?? '');
        })->map(function ($group) {
            $first = $group->first();
            return (object) [
                'title'         => $first->title,
                'author'        => $first->author,
                'publisher'     => $first->publisher,
                'edition'       => $first->edition,
                'isbn'          => $first->isbn,
                'year'          => $first->year,
                'page'          => $first->page,
                'volume'        => $first->volume,
                'price'         => $first->price,
                'total_copies'  => $group->count(),
                'copies'        => $group,
            ];
        })->values();
 
        // Summary stats
        $stats = (object) [
            'total_titles'    => $titleGroups->count(),
            'total_copies'    => $copies->count(),
            'total_amount'    => $copies->sum('price'),
            'available'       => $copies->where('status', 'available')->count(),
            'damaged'         => $copies->where('status', 'damaged')->count(),
            'lost'            => $copies->where('status', 'lost')->count(),
            'under_repair'    => $copies->where('status', 'under_repair')->count(),
        ];
 
        // Library locations referenced in this order
        $locationIds = $copies->pluck('room_id')->filter()
            ->merge($copies->pluck('self_id')->filter())
            ->merge($copies->pluck('row_id')->filter())
            ->unique();
 
        $locations = LibraryMaster::where('tenant_id', $tenantId)
            ->whereIn('id', $locationIds)
            ->get()
            ->keyBy('id');
 
        return view('Library.stock.show', compact(
            'header',
            'titleGroups',
            'copies',
            'stats',
            'locations',
            'orderId'
        ));
    }

    public function edit($orderId)
    {
        $tenantId  = auth()->user()->tenant_id;
        $firstBook = Books::where('tenant_id', $tenantId)->where('order_id', $orderId)->firstOrFail();
 
        $header = (object) [
            'order_id'        => $firstBook->order_id,
            'bill_no'         => $firstBook->bill_no,
            'bill_date'       => $firstBook->bill_date,
            'withdrawal_no'   => $firstBook->withdrawal_no,
            'withdrawal_date' => $firstBook->withdrawal_date,
            'status'          => $firstBook->status,
            'source'          => $firstBook->source,
            'remarks'         => $firstBook->remarks,
            'bill_pdf'        => $firstBook->bill_pdf,
        ];
 
        // One representative row per title (min copy_number = copy 1)
        $titleRows = Books::where('tenant_id', $tenantId)
            ->where('order_id', $orderId)
            ->whereRaw('copy_number = 1')   // one per title group
            ->orderBy('title')
            ->get();
 
        // Attach actual copy count for each title group
        $copyCounts = Books::where('tenant_id', $tenantId)
            ->where('order_id', $orderId)
            ->select('title', 'author', 'publisher', 'edition', DB::raw('COUNT(*) as cnt'))
            ->groupBy('title', 'author', 'publisher', 'edition')
            ->get()
            ->keyBy(fn($r) => $r->title . '|' . ($r->author ?? '') . '|' . ($r->publisher ?? '') . '|' . ($r->edition ?? ''));
 
        $titleRows = $titleRows->map(function ($book) use ($copyCounts) {
            $key = $book->title . '|' . ($book->author ?? '') . '|' . ($book->publisher ?? '') . '|' . ($book->edition ?? '');
            $book->actual_copy_count = $copyCounts[$key]->cnt ?? 1;
            return $book;
        });
 
        return view('Library.stock.edit', compact('header', 'titleRows', 'orderId'));
    }
 
    /**
     * UPDATE
     * Handles the AJAX PUT from the edit form.
     *
     * Strategy:
     *  1. Update shared header fields on ALL copies in this order.
     *  2. For each book row submitted:
     *     a. If qty increased  → create extra copies.
     *     b. If qty decreased  → delete the last N copies (only if not issued).
     *     c. Update shared book fields (title, author, publisher, edition, etc.)
     *        on all copies of that book within this order.
     *  3. Replace the bill PDF if a new one is uploaded.
     */
    public function update(Request $request, $orderId)
    {
        $tenantId = auth()->user()->tenant_id;
 
        // Verify ownership
        $exists = Books::where('tenant_id', $tenantId)->where('order_id', $orderId)->exists();
        if (!$exists) abort(404);
 
        $validator = Validator::make($request->all(), [
            'status'          => 'required|string',
            'bill_no'         => 'nullable|string|max:100',
            'bill_date'       => 'nullable|date',
            'withdrawal_no'   => 'nullable|string|max:100',
            'withdrawal_date' => 'nullable|date',
            'bill_pdf'        => 'nullable|file|mimes:pdf|max:5120',
            'remarks'         => 'nullable|string|max:500',
            'source'          => 'nullable|string|max:100',
 
            'books'                  => 'required|array|min:1',
            'books.*.book_id'        => 'required|integer|exists:books,id',
            'books.*.title'          => 'required|string|max:255',
            'books.*.author'         => 'required|string|max:255',
            'books.*.publisher'      => 'nullable|string|max:255',
            'books.*.edition'        => 'nullable|string|max:100',
            'books.*.volume'         => 'nullable|string|max:50',
            'books.*.year'           => 'nullable|digits:4|max:2026',
            'books.*.page'           => 'nullable|integer|min:1|max:5000',
            'books.*.price'          => 'nullable|numeric|min:0|max:100000',
            'books.*.total_copies'   => 'required|integer|min:1|max:100',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }
 
        DB::beginTransaction();
        try {
            // 1. Handle PDF replacement
            $billPdfPath = null;
            $currentPdf  = Books::where('tenant_id', $tenantId)
                ->where('order_id', $orderId)->value('bill_pdf');
 
            if ($request->hasFile('bill_pdf')) {
                // Delete old PDF
                if ($currentPdf && Storage::disk('public')->exists($currentPdf)) {
                    Storage::disk('public')->delete($currentPdf);
                }
                $billPdfPath = $request->file('bill_pdf')->store('library/bills', 'public');
            } else {
                $billPdfPath = $currentPdf;
            }
 
            // 2. Update shared header on ALL copies in this order
            Books::where('tenant_id', $tenantId)
                ->where('order_id', $orderId)
                ->update([
                    'bill_no'         => $request->bill_no,
                    'bill_date'       => $request->bill_date,
                    'withdrawal_no'   => $request->withdrawal_no,
                    'withdrawal_date' => $request->withdrawal_date,
                    'status'          => $request->status,
                    'source'          => $request->source,
                    'remarks'         => $request->remarks,
                    'bill_pdf'        => $billPdfPath,
                ]);
 
            // 3. Per-book updates
            foreach ($request->books as $bookData) {
                $representativeCopy = Books::where('tenant_id', $tenantId)
                    ->where('id', $bookData['book_id'])
                    ->where('order_id', $orderId)
                    ->firstOrFail();
 
                $oldTitle     = $representativeCopy->title;
                $oldAuthor    = $representativeCopy->author    ?? '';
                $oldPublisher = $representativeCopy->publisher ?? '';
                $oldEdition   = $representativeCopy->edition   ?? '';
 
                $newCopiesWanted = (int) $bookData['total_copies'];
 
                // All existing copies of this specific book title in this order
                $existingCopies = Books::where('tenant_id', $tenantId)
                    ->where('order_id', $orderId)
                    ->where('title', $oldTitle)
                    ->whereRaw('COALESCE(author,"") = ?',    [$oldAuthor])
                    ->whereRaw('COALESCE(publisher,"") = ?', [$oldPublisher])
                    ->whereRaw('COALESCE(edition,"") = ?',   [$oldEdition])
                    ->orderBy('copy_number')
                    ->get();
 
                $currentCount = $existingCopies->count();
 
                // a. Qty decreased → delete last N copies (skip if issued)
                if ($newCopiesWanted < $currentCount) {
                    $toDelete = $existingCopies->sortByDesc('copy_number')
                        ->take($currentCount - $newCopiesWanted);
 
                    foreach ($toDelete as $copy) {
                        // Safety: don't delete if the copy is currently issued
                        $isIssued = DB::table('book_issues')
                            ->where('book_id', $copy->id)
                            ->whereNull('return_date')
                            ->exists();
 
                        if (!$isIssued) {
                            if ($copy->qr_code && Storage::disk('public')->exists($copy->qr_code)) {
                                Storage::disk('public')->delete($copy->qr_code);
                            }
                            $copy->delete();
                        }
                    }
                }
 
                // b. Qty increased → create new copies
                if ($newCopiesWanted > $currentCount) {
                    $isbn          = $representativeCopy->isbn;
                    $maxCopyNumber = $existingCopies->max('copy_number') ?? 0;
 
                    for ($i = $maxCopyNumber + 1; $i <= $maxCopyNumber + ($newCopiesWanted - $currentCount); $i++) {
                        $newCopy = $representativeCopy->replicate();
                        $newCopy->copy_number = $i;
                        $newCopy->copy_isbn   = $isbn . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                        $newCopy->qr_code     = null;
                        // Apply new field values to the new copies too
                        $newCopy->title       = $bookData['title'];
                        $newCopy->author      = $bookData['author'];
                        $newCopy->publisher   = $bookData['publisher']  ?? $representativeCopy->publisher;
                        $newCopy->edition     = $bookData['edition']    ?? $representativeCopy->edition;
                        $newCopy->volume      = $bookData['volume']     ?? $representativeCopy->volume;
                        $newCopy->year        = $bookData['year']       ?? $representativeCopy->year;
                        $newCopy->page        = $bookData['page']       ?? $representativeCopy->page;
                        $newCopy->price       = $bookData['price']      ?? $representativeCopy->price;
                        $newCopy->total_copies = $newCopiesWanted;
                        $newCopy->save();
 
                        // Generate barcode for the new copy
                        app(BooksController::class)->updateBarcode($newCopy->id);
                    }
                }
 
                // c. Update book fields on ALL existing copies of this title in this order
                Books::where('tenant_id', $tenantId)
                    ->where('order_id', $orderId)
                    ->where('title', $oldTitle)
                    ->whereRaw('COALESCE(author,"") = ?',    [$oldAuthor])
                    ->whereRaw('COALESCE(publisher,"") = ?', [$oldPublisher])
                    ->whereRaw('COALESCE(edition,"") = ?',   [$oldEdition])
                    ->update([
                        'title'        => $bookData['title'],
                        'author'       => $bookData['author'],
                        'publisher'    => $bookData['publisher']  ?? null,
                        'edition'      => $bookData['edition']    ?? null,
                        'volume'       => $bookData['volume']     ?? null,
                        'year'         => $bookData['year']       ?? null,
                        'page'         => $bookData['page']       ?? null,
                        'price'        => $bookData['price']      ?? 0,
                        'total_copies' => $newCopiesWanted,
                    ]);
            }
 
            DB::commit();
 
            return response()->json([
                'message'  => 'Stock entry updated successfully!',
                'order_id' => $orderId,
            ]);
 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }
 
    /**
     * DESTROY
     * Deletes ALL book copies in an order_id for this tenant.
     * Skips copies that are currently issued (returns count of skipped).
     */
    public function destroy($orderId)
    {
        $tenantId = auth()->user()->tenant_id;
 
        $copies = Books::where('tenant_id', $tenantId)
            ->where('order_id', $orderId)
            ->get();
 
        if ($copies->isEmpty()) abort(404);
 
        $deleted = 0;
        $skipped = 0;
 
        // Delete the shared bill PDF (only once)
        $billPdf = $copies->first()->bill_pdf;
        if ($billPdf && Storage::disk('public')->exists($billPdf)) {
            Storage::disk('public')->delete($billPdf);
        }
 
        foreach ($copies as $copy) {
            // Don't delete copies that are currently issued
            $isIssued = DB::table('book_issues')
                ->where('book_id', $copy->id)
                ->whereNull('return_date')
                ->exists();
 
            if ($isIssued) {
                $skipped++;
                continue;
            }
 
            if ($copy->qr_code && Storage::disk('public')->exists($copy->qr_code)) {
                Storage::disk('public')->delete($copy->qr_code);
            }
            if ($copy->book_image && Storage::disk('public')->exists($copy->book_image)) {
                Storage::disk('public')->delete($copy->book_image);
            }
            $copy->delete();
            $deleted++;
        }
 
        $msg = "Stock entry deleted: {$deleted} copies removed.";
        if ($skipped > 0) {
            $msg .= " {$skipped} copies skipped (currently issued).";
        }
 
        return redirect()->route('admin.library.stock.index')->with('success', $msg);
    }
}
