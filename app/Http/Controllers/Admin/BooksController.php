<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookIssues;
use App\Models\Books;
use App\Models\LibraryMaster;
use App\Models\AcademicGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;
use PhpOffice\PhpSpreadsheet\IOFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class BooksController extends Controller
{
    protected $booksListRoute = 'admin.books.index';

    public function index(Request $request)
    {
        $query = Books::where('tenant_id', auth()->user()->tenant_id)
            ->select([
                'books.*',
                DB::raw('CONCAT(title, "|", COALESCE(author, ""), "|", COALESCE(publisher, ""), "|", COALESCE(edition, "")) as book_key'),
                DB::raw(
                    '
                (SELECT COUNT(*) 
                 FROM books b2 
                 WHERE CONCAT(b2.title, "|", COALESCE(b2.author, ""), "|", COALESCE(b2.publisher, ""), "|", COALESCE(b2.edition, "")) = 
                       CONCAT(books.title, "|", COALESCE(books.author, ""), "|", COALESCE(books.publisher, ""), "|", COALESCE(books.edition, ""))
                 AND b2.tenant_id = books.tenant_id 
                 AND b2.status = "available") as available_copies'
                ),
                DB::raw(
                    '
                (SELECT COUNT(*) 
                 FROM books b2 
                 WHERE CONCAT(b2.title, "|", COALESCE(b2.author, ""), "|", COALESCE(b2.publisher, ""), "|", COALESCE(b2.edition, ""))
                 = CONCAT(books.title, "|", COALESCE(books.author, ""), "|", COALESCE(books.publisher, ""), "|", COALESCE(books.edition, ""))
                 AND b2.tenant_id = books.tenant_id) as total_copies_actual'
                ),
            ])
            ->whereRaw(
                '
            id IN (
                SELECT MIN(id) 
                FROM books 
                WHERE tenant_id = ? 
                GROUP BY title, COALESCE(author, ""), COALESCE(publisher, ""), COALESCE(edition, "")
            )',
                [auth()->user()->tenant_id]
            );

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('author', 'LIKE', "%{$search}%")
                    ->orWhere('isbn', 'LIKE', "%{$search}%")
                    ->orWhere('publisher', 'LIKE', "%{$search}%");
            });
        }

        // ✅ Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // ✅ Academic Group filter
        if ($request->filled('academic_group')) {
            $query->where('academic_group_id', $request->academic_group);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->whereRaw("
            EXISTS (
                SELECT 1 FROM books b2 
                WHERE CONCAT(b2.title, '|', COALESCE(b2.author, ''), '|', COALESCE(b2.publisher, ''), '|', COALESCE(b2.edition, '')) = 
                      CONCAT(books.title, '|', COALESCE(books.author, ''), '|', COALESCE(books.publisher, ''), '|', COALESCE(books.edition, ''))
                AND b2.tenant_id = books.tenant_id 
                AND b2.status = ?
            )", [$request->status]);
        }

        if ($request->filled('min_available') && $request->min_available > 0) {
            $query->havingRaw('available_copies >= ?', [$request->min_available]);
        }

        $books = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        $books->getCollection()->transform(function ($book) {
            $book->availability_percent = $book->total_copies_actual > 0
                ? round(($book->available_copies / $book->total_copies_actual) * 100)
                : 0;
            return $book;
        });

        $categories = Books::where('tenant_id', auth()->user()->tenant_id)
            ->distinct()->pluck('category')->filter()->sort()->values();

        $statuses = ['available', 'damaged', 'issued', 'lost', 'under_repair'];

        $statsData = [
            'total'     => Books::where('tenant_id', auth()->user()->tenant_id)->count(),
            'available' => Books::where('tenant_id', auth()->user()->tenant_id)->where('status', 'available')->count(),
            'issued'    => Books::where('tenant_id', auth()->user()->tenant_id)->where('status', 'issued')->count(),
            'returned'  => BookIssues::where('tenant_id', auth()->user()->tenant_id)->where('status', 'returned')->count(),
            'damaged'   => Books::where('tenant_id', auth()->user()->tenant_id)->where('status', 'damaged')->count(),
            'lost'      => Books::where('tenant_id', auth()->user()->tenant_id)->where('status', 'lost')->count(),
            'under_repair' => Books::where('tenant_id', auth()->user()->tenant_id)->where('status', 'under_repair')->count(),
        ];

        // ✅ Academic Groups for filter dropdown (simple flat list)
        $academicGroups = AcademicGroups::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('Library.book.index', compact('books', 'categories', 'statuses', 'statsData', 'academicGroups'));
    }

    public function create()
    {
        $books = null;

        $rooms = LibraryMaster::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 1)
            ->where('type', 1)
            ->orderBy('name')
            ->get(['id', 'name']);
        $groups = AcademicGroups::getHierarchyList(auth()->user()->tenant_id);
        $academicGroups = AcademicGroups::buildIndentedOptions($groups);

        return view('Library.book.form', compact('books', 'rooms', 'academicGroups'));
    }

    public function buyBook()
    {
        $books = null;

        $libraryLocations = LibraryMaster::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 1)
            ->get()
            ->groupBy('type');

        $categories = AcademicGroups::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 1)
            ->with('parent')
            ->orderBy('name')
            ->get()
            ->map(function ($group) {
                $group->formatted_name = $this->formatGroupName($group);
                return $group;
            });

        return view('Library.book.buybook', compact('books', 'libraryLocations', 'categories'));
    }

    private function formatGroupName($group)
    {
        $hierarchy = $this->getParentHierarchyReverse($group);

        if (count($hierarchy) > 0) {
            $hierarchyString = implode(' → ', array_reverse($hierarchy));
            return $group->name . ' (' . $hierarchyString . ')';
        }

        return $group->name;
    }

    private function getParentHierarchyReverse($group, &$hierarchy = [])
    {
        if ($group->parent) {
            $hierarchy[] = $group->parent->name;
            $this->getParentHierarchyReverse($group->parent, $hierarchy);
        }
        return $hierarchy;
    }

    public function importPage()
    {
        return view('Library.book.import');
    }

    public function downloadTemplate()
    {
        $path = public_path('templates/books_import_template.xlsx');

        if (!file_exists($path)) {
            $this->generateTemplate($path);
        }

        return response()->download($path, 'books_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function generateTemplate(string $savePath): void
    {
        $source = storage_path('app/public/books_import_template.xlsx');
        if (file_exists($source)) {
            @mkdir(dirname($savePath), 0755, true);
            copy($source, $savePath);
        }
    }

    public function importBooks(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('import_file')->getPathname());
            $sheet       = $spreadsheet->getSheetByName('Books Import')
                ?? $spreadsheet->getActiveSheet();

            $highestRow = $sheet->getHighestDataRow();
            $rows       = $sheet->rangeToArray("A5:P{$highestRow}", null, true, false);

            $results  = ['created' => 0, 'skipped' => 0, 'errors' => []];
            $tenantId = auth()->user()->tenant_id;

            foreach ($rows as $rowIndex => $row) {
                $lineNo = $rowIndex + 5;

                if ($lineNo <= 6) continue;
                if (empty(trim($row[0] ?? ''))) continue;

                $title       = trim($row[0]  ?? '');
                $author      = trim($row[1]  ?? '');
                $publisher   = trim($row[2]  ?? '') ?: 'Auto-Publisher';
                $edition     = trim($row[3]  ?? '') ?: '1st';
                $category    = trim($row[4]  ?? '') ?: 'General';
                $year        = trim($row[5]  ?? '') ?: now()->year;
                $isbn        = trim($row[6]  ?? '');
                $totalCopies = (int) ($row[7] ?? 1);
                $status      = trim($row[8]  ?? 'available');
                $roomName    = trim($row[9]  ?? '');
                $selfName    = trim($row[10] ?? '');
                $rowName     = trim($row[11] ?? '');
                $source      = trim($row[12] ?? '') ?: 'Library';
                $page        = (int) ($row[13] ?? 300) ?: 300;
                $volume      = trim($row[14] ?? '');
                $price       = (float) ($row[15] ?? 0);

                if (empty($title)) {
                    $results['errors'][] = "Row {$lineNo}: Title is required.";
                    $results['skipped']++;
                    continue;
                }
                if (empty($author)) {
                    $results['errors'][] = "Row {$lineNo}: Author is required for '{$title}'.";
                    $results['skipped']++;
                    continue;
                }
                if ($totalCopies < 1 || $totalCopies > 100) {
                    $results['errors'][] = "Row {$lineNo}: total_copies must be 1–100 for '{$title}'.";
                    $results['skipped']++;
                    continue;
                }
                $validStatuses = ['available', 'damaged', 'lost', 'under_repair'];
                if (!in_array($status, $validStatuses)) {
                    $results['errors'][] = "Row {$lineNo}: Invalid status '{$status}' for '{$title}'. Use: " . implode(', ', $validStatuses);
                    $results['skipped']++;
                    continue;
                }

                $roomId = null;
                $selfId = null;
                $rowId  = null;

                if ($roomName) {
                    $loc = LibraryMaster::where('tenant_id', $tenantId)
                        ->where('type', 1)->where('name', $roomName)->first();
                    if ($loc) $roomId = $loc->id;
                    else $results['errors'][] = "Row {$lineNo}: Room '{$roomName}' not found – will use null.";
                }
                if ($selfName) {
                    $loc = LibraryMaster::where('tenant_id', $tenantId)
                        ->where('type', 2)->where('name', $selfName)->first();
                    if ($loc) $selfId = $loc->id;
                }
                if ($rowName) {
                    $loc = LibraryMaster::where('tenant_id', $tenantId)
                        ->where('type', 3)->where('name', $rowName)->first();
                    if ($loc) $rowId = $loc->id;
                }

                $baseIsbn = $isbn ?: $this->generateValidIsbn13();

                DB::transaction(function () use (
                    $tenantId,
                    $title,
                    $author,
                    $publisher,
                    $edition,
                    $category,
                    $year,
                    $baseIsbn,
                    $totalCopies,
                    $status,
                    $roomId,
                    $selfId,
                    $rowId,
                    $source,
                    $page,
                    $volume,
                    $price,
                    &$results
                ) {
                    for ($i = 1; $i <= $totalCopies; $i++) {
                        $book = Books::create([
                            'tenant_id'    => $tenantId,
                            'title'        => $title,
                            'author'       => $author,
                            'publisher'    => $publisher,
                            'edition'      => $edition,
                            'category'     => $category,
                            'year'         => $year,
                            'isbn'         => $baseIsbn,
                            'copy_isbn'    => $baseIsbn . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                            'copy_number'  => $i,
                            'total_copies' => $totalCopies,
                            'status'       => $status,
                            'room_id'      => $roomId,
                            'self_id'      => $selfId,
                            'row_id'       => $rowId,
                            'source'       => $source,
                            'page'         => $page,
                            'volume'       => $volume ?: null,
                            'price'        => $price,
                        ]);
                        $this->generateEAN13Barcode($book);
                        $results['created']++;
                    }
                });
            }

            $msg = "✅ Import complete: {$results['created']} book copies created";
            if ($results['skipped']) {
                $msg .= ", {$results['skipped']} rows skipped";
            }

            return redirect()->route('admin.books.import')
                ->with('import_success', $msg)
                ->with('import_errors', $results['errors']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Import failed: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:255',
            'total_copies'     => 'required_without:id|nullable|integer|min:1|max:100',
            'publisher'        => 'nullable|string|max:255',
            'edition'          => 'nullable|string|max:100',
            'category'         => 'nullable|string|max:100',
            'year'             => 'nullable|digits:4|max:2026',
            'source'           => 'nullable|string|max:100',
            'page'             => 'nullable|integer|min:1|max:5000',
            'volume'           => 'nullable|string|max:50',
            'price'            => 'nullable|numeric|min:0|max:100000',
            'room_id'          => 'required|exists:library_master,id',
            'self_id'          => 'nullable|exists:library_master,id',
            'row_id'           => 'nullable|exists:library_master,id',
            'status'           => 'required|in:available,damaged,lost,under_repair',
            'book_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'academic_group_id' => 'nullable|exists:academic_groups,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->filled('id')) {
            return $this->updateBook($request);
        } else {
            return $this->createBooks($request);
        }
    }

    private function createBooks(Request $request)
    {
        $masterBook = null;

        for ($i = 1; $i <= $request->total_copies; $i++) {
            $book = new Books();
            $book->fill($this->getBookData($request, $i));
            $book->save();

            if ($i == 1) $masterBook = $book;

            $this->generateEAN13Barcode($book);
        }

        return redirect()->route($this->booksListRoute)
            ->with('success', "✅ Created {$request->total_copies} copies of '{$request->title}'!");
    }

    private function updateBook(Request $request)
    {
        $book = Books::findOrFail($request->id);
        $book->fill($this->getBookData($request));
        $book->save();

        if ($request->hasFile('book_image')) {
            if ($book->book_image && Storage::disk('public')->exists($book->book_image)) {
                Storage::disk('public')->delete($book->book_image);
            }
            $book->book_image = $request->file('book_image')->store('books/images', 'public');
            $book->save();
        }

        return redirect()->route($this->booksListRoute)
            ->with('success', "✅ Updated '{$request->title}' copy!");
    }

    private function generateValidIsbn13()
    {
        $random12 = '978' . str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $random12[$i];
            $sum += ($i % 2 == 0) ? $digit : $digit * 3;
        }
        $checkDigit = (10 - ($sum % 10)) % 10;

        return $random12 . $checkDigit;
    }

    private function getBookData(Request $request, $copyNumber = null)
    {
        $isbn = $request->isbn ?: $this->generateValidIsbn13();

        $data = [
            'tenant_id'         => auth()->user()->tenant_id,
            'title'             => $request->title,
            'author'            => $request->author,
            'isbn'              => $isbn,
            'publisher'         => $request->publisher ?? 'Auto-Publisher',
            'edition'           => $request->edition ?? '1st',
            'category'          => $request->category ?? 'General',
            'year'              => $request->year ?? now()->year,
            'source'            => $request->source ?? 'Library',
            'page'              => $request->page ?? 300,
            'volume'            => $request->volume,
            'price'             => $request->price ?? 0,
            'room_id'           => $request->room ?? 1,
            'self_id'           => $request->self,
            'row_id'            => $request->row,
            'status'            => $request->status,
            'academic_group_id' => $request->academic_group_id,
        ];

        if ($request->filled('total_copies')) {
            $data['total_copies'] = $request->total_copies;
        } elseif ($request->filled('id')) {
            $book = Books::findOrFail($request->id);
            $data['total_copies'] = $book->total_copies;
        }

        if ($request->filled('id') && $copyNumber === null) {
            $book = Books::findOrFail($request->id);
            $data['copy_number'] = $book->copy_number;
            $data['copy_isbn']   = $book->copy_isbn;
        } elseif ($copyNumber !== null) {
            $data['copy_number'] = $copyNumber;
            $data['copy_isbn']   = $isbn . '-' . str_pad($copyNumber, 3, '0', STR_PAD_LEFT);
        }

        if ($request->hasFile('book_image') && !$request->filled('id')) {
            $data['book_image'] = $request->file('book_image')->store('books/images', 'public');
        } elseif ($request->filled('id') && $request->hasFile('book_image')) {
            $data['book_image'] = $request->file('book_image')->store('books/images', 'public');
        }

        return $data;
    }

    private function generateEAN13Barcode($book)
    {
        if (empty($book->isbn) || strlen($book->isbn) !== 13) {
            return;
        }

        $barcodePath = 'books/ean13/ean_' . $book->isbn . '_' . $book->id . '.png';
        Storage::disk('public')->makeDirectory('books/ean13', 0755, true);

        try {
            $width  = 400;
            $height = 150;
            $image  = imagecreatetruecolor($width, $height);

            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            imagefill($image, 0, 0, $white);

            $barcode = new DNS1D();
            $barcode->setStorPath(storage_path('app/barcodes/'));
            $barcodePNG = $barcode->getBarcodePNG($book->isbn, 'EAN13', 3, 80, [0, 0, 0]);

            $barcodeImage = imagecreatefromstring(base64_decode($barcodePNG));

            $barcodeW = imagesx($barcodeImage);
            $barcodeH = imagesy($barcodeImage);
            $x = (int)(($width - $barcodeW) / 2);
            $y = (int)(($height - $barcodeH) / 2) - 10;

            imagecopy($image, $barcodeImage, $x, $y, 0, 0, $barcodeW, $barcodeH);
            // imagestring($image, 3, (int)(($width - strlen($book->isbn) * 7) / 2), $y + $barcodeH + 5, $book->isbn, $black);

            ob_start();
            imagepng($image);
            $imageData = ob_get_clean();

            imagedestroy($image);
            imagedestroy($barcodeImage);

            Storage::disk('public')->put($barcodePath, $imageData);

            $book->qr_code = $barcodePath;
            $book->save();
        } catch (\Exception $e) {
            \Log::error('Barcode generation failed: ' . $e->getMessage());
        }
    }

    public function updateBarcode($bookId)
    {
        $book = Books::where('id', $bookId)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if ($book && !empty($book->isbn)) {
            $this->generateEAN13Barcode($book);
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Invalid book'], 404);
    }

    public function edit($id)
    {
        $books = Books::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (!$books) {
            abort(404);
        }

        $rooms = LibraryMaster::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 1)
            ->where('type', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $groups         = AcademicGroups::getHierarchyList(auth()->user()->tenant_id);
        $academicGroups = AcademicGroups::buildIndentedOptions($groups);

        return view('Library.book.form', compact('books', 'rooms', 'academicGroups'));
    }

    public function show($bookKey)
    {
        // Decode base64-encoded book_key from URL
        $bookKey = base64_decode($bookKey);

        [$title, $author, $publisher, $edition] = explode('|', $bookKey, 4);
        $tenantId = auth()->user()->tenant_id;
        $masterBook = Books::where('title', $title)
            ->whereRaw('COALESCE(author, "") = ?', [$author])
            ->whereRaw('COALESCE(publisher, "") = ?', [$publisher])
            ->whereRaw('COALESCE(edition, "") = ?', [$edition])
            ->where('tenant_id', $tenantId)
            ->whereRaw('
            id = (
                SELECT MIN(id) 
                FROM books b2
                WHERE b2.title = ? 
                AND COALESCE(b2.author, "") = ? 
                AND COALESCE(b2.publisher, "") = ? 
                AND COALESCE(b2.edition, "") = ? 
                AND b2.tenant_id = ?
            )', [$title, $author, $publisher, $edition, $tenantId])
            ->firstOrFail();

        $copies = Books::where('title', $title)
            ->whereRaw('COALESCE(author, "") = ?', [$author])
            ->whereRaw('COALESCE(publisher, "") = ?', [$publisher])
            ->whereRaw('COALESCE(edition, "") = ?', [$edition])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('copy_number')
            ->get();

        $stats = [
            'total'     => $copies->count(),
            'available' => $copies->where('status', 'available')->count(),
            'damaged'   => $copies->where('status', 'damaged')->count(),
            'lost'      => $copies->where('status', 'lost')->count(),
            'repair'    => $copies->where('status', 'under_repair')->count(),
        ];

        $libraryLocationIds = $copies->pluck('room_id')->filter()
            ->merge($copies->pluck('self_id')->filter())
            ->merge($copies->pluck('row_id')->filter())
            ->unique();

        $libraryLocations = LibraryMaster::where('tenant_id', auth()->user()->tenant_id)
            ->whereIn('id', $libraryLocationIds)
            ->get()
            ->keyBy('id');

        $issues = BookIssues::whereHas('book', function ($q) use ($title, $author, $publisher, $edition) {
            $q->where('title', $title)
                ->whereRaw('COALESCE(author, "") = ?', [$author])
                ->whereRaw('COALESCE(publisher, "") = ?', [$publisher])
                ->whereRaw('COALESCE(edition, "") = ?', [$edition])
                ->where('tenant_id', auth()->user()->tenant_id);
        })
            ->with(['student', 'staff'])
            ->orderBy('issue_date', 'desc')
            ->paginate(10);

        return view('Library.book.show', compact(
            'masterBook',
            'copies',
            'stats',
            'libraryLocations',
            'issues',
            'bookKey'
        ));
    }

    // public function destroy($id)
    // {
    //     $book = Books::where('id', $id)
    //         ->where('tenant_id', auth()->user()->tenant_id)
    //         ->first();

    //     if (!$book) {
    //         return redirect()->back()->with('error', 'Book not found!');
    //     }

    //     try {
    //         if ($book->book_image && Storage::disk('public')->exists($book->book_image)) {
    //             Storage::disk('public')->delete($book->book_image);
    //         }
    //         if ($book->qr_code && Storage::disk('public')->exists($book->qr_code)) {
    //             Storage::disk('public')->delete($book->qr_code);
    //         }
    //         $book->delete();
    //         return redirect()->back()->with('success', 'Book deleted successfully');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Something went wrong while deleting!');
    //     }
    // }
    public function destroy($id)
    {
        $book = Books::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (!$book) {
            return redirect()->back()->with('error', 'Book not found!');
        }

        // Store book details before deleting to check remaining copies
        $title     = $book->title;
        $author    = $book->author;
        $publisher = $book->publisher;
        $edition   = $book->edition;

        try {
            if ($book->book_image && Storage::disk('public')->exists($book->book_image)) {
                Storage::disk('public')->delete($book->book_image);
            }
            if ($book->qr_code && Storage::disk('public')->exists($book->qr_code)) {
                Storage::disk('public')->delete($book->qr_code);
            }
            $book->delete();

            // Check if any copies still exist for this book group
            $remainingCopies = Books::where('tenant_id', auth()->user()->tenant_id)
                ->where('title', $title)
                ->whereRaw('COALESCE(author, "") = ?', [$author ?? ''])
                ->whereRaw('COALESCE(publisher, "") = ?', [$publisher ?? ''])
                ->whereRaw('COALESCE(edition, "") = ?', [$edition ?? ''])
                ->count();

            if ($remainingCopies === 0) {
                return redirect()->route('admin.books.index')
                    ->with('success', 'Last copy deleted. Book removed from library.');
            }

            return redirect()->back()->with('success', 'Book copy deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong while deleting!');
        }
    }
    public function scanPage()
    {
        return view('Library.book.scan');
    }

    public function scanBarcode(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);
        $barcode = trim($request->barcode);

        $book = Books::where('tenant_id', auth()->user()->tenant_id)
            ->where('copy_isbn', $barcode)->first();

        if (!$book) {
            $book = Books::where('tenant_id', auth()->user()->tenant_id)
                ->where('isbn', $barcode)->first();
        }

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found with this barcode: ' . $barcode
            ]);
        }

        $copies = Books::where('tenant_id', auth()->user()->tenant_id)
            ->where('isbn', $book->isbn)
            ->orderBy('copy_number')
            ->get();

        $stats = [
            'total'     => $copies->count(),
            'available' => $copies->where('status', 'available')->count(),
            'damaged'   => $copies->where('status', 'damaged')->count(),
            'lost'      => $copies->where('status', 'lost')->count(),
            'repair'    => $copies->where('status', 'under_repair')->count(),
            'issued'    => $copies->where('status', 'issued')->count(),
        ];

        $locations = [];
        if ($book->room_id) $locations['room'] = LibraryMaster::find($book->room_id);
        if ($book->self_id) $locations['self'] = LibraryMaster::find($book->self_id);
        if ($book->row_id)  $locations['row']  = LibraryMaster::find($book->row_id);

        $allCopyIds = $copies->pluck('id');

        $issueHistory = BookIssues::whereIn('book_id', $allCopyIds)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['student', 'staff', 'book'])
            ->orderBy('issue_date', 'desc')
            ->get()
            ->map(function ($issue) {
                return [
                    'id'          => $issue->id,
                    'copy_number' => $issue->book->copy_number ?? 'N/A',
                    'copy_isbn'   => $issue->book->copy_isbn ?? 'N/A',
                    'issued_to'   => $issue->student
                        ? $issue->student->first_name . ' ' . $issue->student->last_name
                        : ($issue->staff
                            ? $issue->staff->first_name . ' ' . $issue->staff->last_name
                            : 'N/A'),
                    'type'        => $issue->student_id ? 'Student' : 'Staff',
                    'issue_date'  => $issue->issue_date
                        ? \Carbon\Carbon::parse($issue->issue_date)->format('d M Y')
                        : 'N/A',
                    'due_date'    => $issue->due_date
                        ? \Carbon\Carbon::parse($issue->due_date)->format('d M Y')
                        : 'N/A',
                    'return_date' => $issue->return_date
                        ? \Carbon\Carbon::parse($issue->return_date)->format('d M Y')
                        : null,
                    'status'      => $issue->status ?? 'issued',
                    'fine'        => $issue->fine_amount ?? 0,
                ];
            });

        return response()->json([
            'success'       => true,
            'book'          => $book,
            'copies'        => $copies,
            'stats'         => $stats,
            'locations'     => $locations,
            'issue_history' => $issueHistory,
            'is_copy'       => true,
            'is_master'     => false,
        ]);
    }

    public function stockStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status'                     => 'required|string',
            'bill_no'                    => 'nullable|string|max:100',
            'bill_date'                  => 'nullable|date',
            'withdrawal_no'              => 'nullable|string|max:100',
            'withdrawal_date'            => 'nullable|date',
            'bill_pdf'                   => 'nullable|file|mimes:pdf|max:5120',
            'remarks'                    => 'nullable|string|max:500',
            'books'                      => 'required|array|min:1',
            'books.*.title'              => 'required|string|max:255',
            'books.*.author'             => 'required|string|max:255',
            'books.*.isbn'               => 'nullable|string|max:20',
            'books.*.publisher'          => 'nullable|string|max:255',
            'books.*.edition'            => 'nullable|string|max:100',
            'books.*.category'           => 'nullable|string|max:100',
            'books.*.year'               => 'nullable|digits:4|max:2026',
            'books.*.source'             => 'nullable|string|max:100',
            'books.*.page'               => 'nullable|integer|min:1|max:5000',
            'books.*.volume'             => 'nullable|string|max:50',
            'books.*.price'              => 'nullable|numeric|min:0|max:100000',
            'books.*.total_copies'       => 'required|integer|min:1|max:100',
            'books.*.room'               => 'nullable|exists:library_master,id',
            'books.*.self'               => 'nullable|exists:library_master,id',
            'books.*.row'                => 'nullable|exists:library_master,id',
            'books.*.book_status'        => 'nullable|in:available,damaged,lost,under_repair',
            'books.*.academic_group_id'  => 'nullable|exists:academic_groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $orderId = 'ORD-' . strtoupper(uniqid());

            $billPdfPath = null;
            if ($request->hasFile('bill_pdf')) {
                $billPdfPath = $request->file('bill_pdf')->store('library/bills', 'public');
            }

            $headerData = [
                'order_id'        => $orderId,
                'bill_no'         => $request->bill_no,
                'bill_date'       => $request->bill_date,
                'withdrawal_no'   => $request->withdrawal_no,
                'withdrawal_date' => $request->withdrawal_date,
                'bill_pdf'        => $billPdfPath,
                'remarks'         => $request->remarks,
            ];

            $totalCopiesCreated = 0;

            foreach ($request->books as $bookData) {
                $isbn = !empty($bookData['isbn'])
                    ? $bookData['isbn']
                    : $this->generateValidIsbn13();

                for ($i = 1; $i <= $bookData['total_copies']; $i++) {
                    $book = new Books();
                    $book->fill(array_merge($headerData, [
                        'tenant_id'         => auth()->user()->tenant_id,
                        'title'             => $bookData['title'],
                        'author'            => $bookData['author'],
                        'isbn'              => $isbn,
                        'publisher'         => $bookData['publisher']         ?? 'Auto-Publisher',
                        'edition'           => $bookData['edition']           ?? '1st',
                        'category'          => $bookData['category']          ?? null,
                        'year'              => $bookData['year']              ?? now()->year,
                        'source'            => $bookData['source']            ?? $request->source ?? 'Library',
                        'page'              => $bookData['page']              ?? 300,
                        'volume'            => $bookData['volume']            ?? null,
                        'price'             => $bookData['price']             ?? 0,
                        'total_copies'      => $bookData['total_copies'],
                        'copy_number'       => $i,
                        'copy_isbn'         => $isbn . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'room_id'           => $bookData['room']              ?? null,
                        'self_id'           => $bookData['self']              ?? null,
                        'row_id'            => $bookData['row']               ?? null,
                        'status'            => $bookData['book_status']       ?? 'available',
                        'academic_group_id' => $bookData['academic_group_id'] ?? null,
                    ]));
                    $book->save();

                    $this->generateEAN13Barcode($book);
                    $totalCopiesCreated++;
                }
            }

            DB::commit();

            return response()->json([
                'message'        => "{$totalCopiesCreated} book copies created successfully!",
                'order_id'       => $orderId,
                'copies_created' => $totalCopiesCreated,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}