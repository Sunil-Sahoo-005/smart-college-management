<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NoticeBoard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoticeBoardController extends Controller
{
   public function index()
{
    $notices = NoticeBoard::where('tenant_id', auth()->user()->tenant_id)
        ->latest('notice_date')
        ->get();

    $noticesJson = $notices->map(function ($notice, $index) {
        return [
            'id'          => $notice->id,
            'serial'      => $index + 1,
            'title'       => $notice->title,
            'notice_date' => $notice->notice_date
                ? \Carbon\Carbon::parse($notice->notice_date)->format('d/m/Y')
                : '–',
            'notice_date_raw' => $notice->notice_date,
            'created_at'  => $notice->created_at
                ? $notice->created_at->format('d/m/Y')
                : '–',
            'file_url'    => $notice->file_path
                ? asset('storage/' . $notice->file_path)
                : null,
            'edit_url'    => route('admin.notice_boards.edit', $notice),
            'delete_url'  => route('admin.notice_boards.destroy', $notice),
        ];
    });

    return view('notice_boards.index', compact('notices', 'noticesJson'));
}

    public function create()
    {
        return view('notice_boards.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'notice_date' => 'required|date',
            'file_path'   => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'title.required'       => 'Notice title is required.',
            'title.string'         => 'Notice title must be a valid text.',
            'notice_date.required' => 'Notice date is required.',
            'notice_date.date'     => 'Please enter a valid date.',
            'file_path.required'   => 'Please upload a file.',
            'file_path.file'       => 'The uploaded file is not valid.',
            'file_path.mimes'      => 'Only PDF, DOC, DOCX, JPG, PNG files are allowed.',
            'file_path.max'        => 'File size must not exceed 5MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('file_path')) {
            $filePath = $request->file('file_path')->store('notice_boards', 'public');
        }

        NoticeBoard::create([
            'tenant_id'   => auth()->user()->tenant_id ?? null,
            'title'       => $request->title,
            'notice_date' => $request->notice_date,
            'file_path'   => $filePath,
        ]);

        return redirect()
            ->route('admin.notice_boards.index')
            ->with('success', 'Notice added successfully.');
    }

    public function edit(NoticeBoard $noticeBoard)
    {
        $notice = $noticeBoard;
        return view('notice_boards.form', compact('notice'));
    }

    public function update(Request $request, NoticeBoard $noticeBoard)
    {
        $request->validate([
            'title'       => 'required|string',
            'notice_date' => 'required|date',
            'file_path'   => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], [
            'title.required'       => 'Notice title is required.',
            'title.string'         => 'Notice title must be a valid text.',
            'notice_date.required' => 'Notice date is required.',
            'notice_date.date'     => 'Please enter a valid date.',
            'file_path.file'       => 'The uploaded file is not valid.',
            'file_path.mimes'      => 'Only PDF, DOC, DOCX, JPG, PNG files are allowed.',
            'file_path.max'        => 'File size must not exceed 5MB.',
        ]);

        $data = [
            'title'       => $request->title,
            'notice_date' => $request->notice_date,
        ];

        if ($request->hasFile('file_path')) {
            if ($noticeBoard->file_path) {
                Storage::disk('public')->delete($noticeBoard->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('notice_boards', 'public');
        }

        $noticeBoard->update($data);

        return redirect()
            ->route('admin.notice_boards.index')
            ->with('success', 'Notice updated successfully.');
    }

    public function destroy(NoticeBoard $noticeBoard)
    {
        if ($noticeBoard->file_path) {
            Storage::disk('public')->delete($noticeBoard->file_path);
        }

        $noticeBoard->delete();

        return redirect()
            ->route('admin.notice_boards.index')
            ->with('success', 'Notice deleted successfully.');
    }
}
