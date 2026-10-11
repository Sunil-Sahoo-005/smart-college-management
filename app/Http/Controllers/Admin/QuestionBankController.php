<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Course;
use App\Models\Stream;
use App\Models\Classe;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class QuestionBankController extends Controller
{
    protected $listRoute = 'admin.question_banks.index';

    // ══════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════
    public function index(Request $request)
{
    
    $questionBanks = QuestionBank::where('tenant_id', Auth::user()->tenant_id)
        ->with(['course', 'stream', 'studentClass', 'subject'])
        ->latest()
        ->get();

    $courses = Course::where('tenant_id', Auth::user()->tenant_id)
        ->with(['streams' => function ($q) {
            $q->where('streams.tenant_id', Auth::user()->tenant_id)
              ->orderBy('streams.name');
        }])
        ->orderBy('name')
        ->get();

    $streams  = Stream::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
    $classes  = Classe::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
    $subjects = Subject::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();

   $coursesJson = $courses->map(fn($c) => [
    'id'      => $c->id,
    'name'    => $c->name,
    'code'    => $c->code,
    'streams' => $c->streams->unique('id')
                   ->map(fn($s) => [
                       'id'        => $s->id,
                       'name'      => $s->name,
                       'course_id' => $c->id,  // 👈 YEH ADD KARO
                   ])
                   ->values()
                   ->toArray(),
                   
]);
// dd($coursesJson);

    $questionBanksJson = $questionBanks->map(fn($qb) => [
        'id'           => $qb->id,
        'name'         => $qb->name,
        'course_id'    => $qb->course_id,
        'stream_id'    => $qb->stream_id,
        'class_name'   => $qb->studentClass?->name ?? '—',
        'subject_name' => $qb->subject?->name ?? '—',
        'description'  => $qb->description,
        'status'       => $qb->status,
        'edit_url'     => route('admin.question_banks.edit', $qb->id),
        'delete_url'   => route('admin.question_banks.destroy', $qb->id),
        'next_url'     => route('admin.question_paper_types.create', ['question_bank_id' => $qb->id]),
    ]);

    return view('Academics.question_banks.index', compact(
        'questionBanks', 'questionBanksJson', 'courses', 'streams', 'classes', 'subjects', 'coursesJson'
    ));
}

    // ══════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════
    public function create(Request $request)
    {
        $questionBank = new QuestionBank;

        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->with(['streams' => function ($q) {
                $q->where('streams.tenant_id', Auth::user()->tenant_id)
                  ->orderBy('streams.name');
            }])
            ->orderBy('name')
            ->get();

        $streams  = Stream::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $classes  = Classe::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $subjects = Subject::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();

        return view('Academics.question_banks.form', compact(
            'questionBank', 'courses', 'streams', 'classes', 'subjects'
        ));
    }

    // ══════════════════════════════════════
    // STORE
    // ══════════════════════════════════════
    public function store(Request $request)
    {
        $rules = [
            'name'        => 'required|string|max:255',
            'course_id'   => 'required|exists:courses,id',
            'stream_id'   => 'required|exists:streams,id',
            'class_id'    => 'required|exists:classes,id',
            'subject_id'  => 'required|exists:subjects,id',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ];

        $messages = [
            'name.required'       => 'Please enter question bank name!',
            'course_id.required'  => 'Please select course!',
            'stream_id.required'  => 'Please select branch!',
            'class_id.required'   => 'Please select class!',
            'subject_id.required' => 'Please select subject!',
            'status.required'     => 'Please select status!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->filled('id')) {
            $questionBank = QuestionBank::where('tenant_id', Auth::user()->tenant_id)
                ->findOrFail($request->id);
            $isNew = false;
        } else {
            $questionBank = new QuestionBank;
            $isNew = true;
        }

        $questionBank->tenant_id   = Auth::user()->tenant_id;
        $questionBank->course_id   = $request->course_id;
        $questionBank->stream_id   = $request->stream_id;
        $questionBank->class_id    = $request->class_id;
        $questionBank->subject_id  = $request->subject_id;
        $questionBank->name        = trim($request->name);
        $questionBank->description = $request->description;
        $questionBank->status      = $request->status ?? 1;

        try {
            $questionBank->save();

            if ($isNew) {
                return redirect()
                    ->route('admin.question_paper_types.create', [
                        'question_bank_id' => $questionBank->id
                    ])
                    ->with('success', 'Question Bank created successfully. Now add Question Paper Type.');
            }

            return redirect()
                ->route($this->listRoute)
                ->with('success', 'Question Bank updated successfully');

        } catch (\Exception $e) {
            \Log::error('QuestionBank save error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ══════════════════════════════════════
    // EDIT
    // ══════════════════════════════════════
    public function edit($id)
    {
        $questionBank = QuestionBank::where('tenant_id', Auth::user()->tenant_id)
            ->with(['course', 'stream', 'studentClass', 'subject'])
            ->findOrFail($id);

        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->with(['streams' => function ($q) {
                $q->where('streams.tenant_id', Auth::user()->tenant_id)
                  ->orderBy('streams.name');
            }])
            ->orderBy('name')
            ->get();

        $streams  = Stream::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $classes  = Classe::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $subjects = Subject::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();

        return view('Academics.question_banks.form', compact(
            'questionBank', 'courses', 'streams', 'classes', 'subjects'
        ));
    }

    // ══════════════════════════════════════
    // DESTROY
    // ══════════════════════════════════════
    public function destroy($id)
    {
        try {
            $questionBank = QuestionBank::where('tenant_id', Auth::user()->tenant_id)
                ->findOrFail($id);

            if ($questionBank->questionPaperTypes()->exists()) {
                return redirect()->route($this->listRoute)
                    ->with('error', 'Cannot delete! This Question Bank has associated Question Paper Types.');
            }

            $questionBank->delete();
            return redirect()->route($this->listRoute)
                ->with('success', 'Question Bank deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('QuestionBank delete error: ' . $e->getMessage());
            return redirect()->route($this->listRoute)
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}