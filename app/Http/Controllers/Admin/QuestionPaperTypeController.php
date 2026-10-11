<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\QuestionPaperType;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionPaperTypeController extends Controller
{
    protected $listRoute = 'admin.question_banks.index';

    // ══════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════
   public function index(Request $request)
{
    $questionBankId = $request->query('question_bank_id');

    $questionBank = QuestionBank::where('tenant_id', Auth::user()->tenant_id)
                        ->findOrFail($questionBankId);

    // ✅ DB::table use karo — model ki zarurat nahi
    $questions = DB::table('questions')
                    ->where('question_bank_id', $questionBankId)
                    ->latest()
                    ->get();

    // ✅ Options attach karo
    $questionIds = $questions->pluck('id');
    $options     = DB::table('question_options')
                        ->whereIn('question_id', $questionIds)
                        ->get()
                        ->groupBy('question_id');

    $questions = $questions->map(function ($q) use ($options) {
        $q->options = $options[$q->id] ?? collect();
        return $q;
    });

    return view('Academics.question_banks.questions_list', compact(
        'questionBank', 'questions'
    ));
}

    // ══════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════
    public function create(Request $request)
    {
        $questionBankId = $request->query('question_bank_id');

        $questionBank = QuestionBank::where('tenant_id', Auth::user()->tenant_id)
                            ->findOrFail($questionBankId);

        $question = new QuestionPaperType;

        return view('Academics.question_banks.questionpapertype', compact(
            'questionBank', 'question'
        ));
    }
        public function toggleStatus(Request $request, $id)
        {
            $question = DB::table('questions')->where('id', $id)->first();
            
            if (!$question) {
                return response()->json(['success' => false, 'message' => 'Question not found.'], 404);
            }

            DB::table('questions')->where('id', $id)->update([
                'is_active'  => $request->input('is_active', 1),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Question status updated.',
                'is_active' => $request->input('is_active', 1),
            ]);
        }
    

    public function store(Request $request)
{
    $rules = [
        'question_bank_id' => 'required|exists:question_banks,id',
        'type'             => 'required|in:mcq,true_false,short_answer,long_answer',
        'question'         => 'required|string',
        'mark'             => 'required|numeric|min:0',
        'negative_marks'   => 'nullable|numeric|min:0',
        'explanation'      => 'nullable|string',
        'is_active'        => 'required|boolean',
    ];

    if ($request->type === 'mcq') {
        $rules['options']         = 'required|array|min:2';
        $rules['options.*']       = 'required|string';
        $rules['correct_option']  = 'required|integer';
    }

    if ($request->type === 'true_false') {
        $rules['true_false_answer'] = 'required|in:true,false';
    }

    $messages = [
        'type.required'           => 'Please select question type!',
        'question.required'       => 'Please enter question!',
        'mark.required'           => 'Please enter marks!',
        'is_active.required'      => 'Please select status!',
        'options.required'        => 'Please add at least 2 options for MCQ!',
        'correct_option.required' => 'Please select correct option!',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    DB::beginTransaction();
    try {
        // dd($request->all());
        // ✅ 1. questions table pe save karo
        if ($request->filled('id')) {
            $questionId = $request->id;
            DB::table('questions')->where('id', $questionId)->update([
                'question_bank_id' => $request->question_bank_id,
                'type'             => $request->type,
                'question'         => trim($request->question),
                'mark'             => $request->mark,
                'negative_marks'   => $request->negative_marks ?? 0,
                'explanation'      => $request->explanation,
                'is_active'        => $request->is_active,
                'updated_at'       => now(),
            ]);
            $isNew = false;
        } else {
            $questionId = DB::table('questions')->insertGetId([
                'question_bank_id' => $request->question_bank_id,
                'type'             => $request->type,
                'question'         => trim($request->question),
                'mark'             => $request->mark,
                'negative_marks'   => $request->negative_marks ?? 0,
                'explanation'      => $request->explanation,
                'is_active'        => $request->is_active,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
            $isNew = true;
        }

        // ✅ 2. question_paper_types table pe bhi save karo
        QuestionPaperType::updateOrCreate(
            [
                'question_bank_id' => $request->question_bank_id,
                'type_name'        => $request->type,
            ],
            [
                'tenant_id'       => Auth::user()->tenant_id,
                'total_marks'     => $request->mark,
                'duration'        => 0,
                'total_questions' => DB::table('questions')
                                        ->where('question_bank_id', $request->question_bank_id)
                                        ->where('type', $request->type)
                                        ->count() + ($isNew ? 1 : 0),
                'description'     => null,
            ]
        );

        // ✅ 3. MCQ Options save karo
        if ($request->type === 'mcq') {
            DB::table('question_options')->where('question_id', $questionId)->delete();
            foreach ($request->options as $index => $optionText) {
                if (!empty($optionText)) {
                    DB::table('question_options')->insert([
                        'question_id' => $questionId,
                        'option_text' => $optionText,
                        'is_correct'  => ($index == $request->correct_option) ? 1 : 0,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        }

        // ✅ 4. True/False Options save karo
        if ($request->type === 'true_false') {
            DB::table('question_options')->where('question_id', $questionId)->delete();
            DB::table('question_options')->insert([
                ['question_id' => $questionId, 'option_text' => 'True',  'is_correct' => $request->true_false_answer === 'true'  ? 1 : 0, 'created_at' => now(), 'updated_at' => now()],
                ['question_id' => $questionId, 'option_text' => 'False', 'is_correct' => $request->true_false_answer === 'false' ? 1 : 0, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

       DB::commit();

// ── Action type check ────────────────────────────────
if ($request->action_type === 'next') {
    return redirect()
        ->route('admin.question_paper_types.create', [
            'question_bank_id' => $request->question_bank_id,
            'default_type'     => $request->type
        ])
        ->with('success', 'Question saved! Add next question.');
}

return redirect()
    ->route('admin.question_paper_types.index', [
        'question_bank_id' => $request->question_bank_id
    ])
    ->with('success', $isNew ? 'Question added successfully' : 'Question updated successfully');

// NAYA
if ($request->action_type === 'next') {
    return redirect()
        ->route('admin.question_paper_types.create', [
            'question_bank_id' => $request->question_bank_id,
            'default_type'     => $request->type
        ])
        ->with('success', 'Question saved! Add next question.');
}

return redirect()
    ->route('admin.question_paper_types.index', [
        'question_bank_id' => $request->question_bank_id
    ])
    ->with('success', $isNew ? 'Question added successfully' : 'Question updated successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Question save error: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Something went wrong: ' . $e->getMessage())
            ->withInput();
    }
}

    // ══════════════════════════════════════
    // EDIT
    // ══════════════════════════════════════
    // public function edit($id)
    // {
    //     $question = QuestionPaperType::with(['questionBank', 'options'])
    //                     ->findOrFail($id);

    //     $questionBank = $question->questionBank;

    //     return view('Academics.question_banks.questionpapertype', compact(
    //         'questionBank', 'question'
    //     ));
    // }

    public function edit($id)
{
    $q = DB::table('questions')->where('id', $id)->first();
    if (!$q) abort(404);

    $questionBank = QuestionBank::where('tenant_id', Auth::user()->tenant_id)
                        ->findOrFail($q->question_bank_id);

    $q->options = DB::table('question_options')
                    ->where('question_id', $id)
                    ->get();

    $question = (object)(array)$q;

    return view('Academics.question_banks.questionpapertype', compact(
        'questionBank', 'question'
    ));
}

    // ══════════════════════════════════════
    // DESTROY
    // ══════════════════════════════════════
    // public function destroy($id)
    // {
    //     try {
    //         $question       = QuestionPaperType::findOrFail($id);
    //         $questionBankId = $question->question_bank_id;

    //         $question->options()->delete();
    //         $question->delete();

    //         return redirect()
    //             ->route('admin.question_paper_types.create', [
    //                 'question_bank_id' => $questionBankId
    //             ])
    //             ->with('success', 'Question deleted successfully');

    //     } catch (\Exception $e) {
    //         \Log::error('Question delete error: ' . $e->getMessage());
    //         return redirect()->back()
    //             ->with('error', 'Something went wrong: ' . $e->getMessage());
    //     }
    // }

    public function destroy($id)
{
    try {
        $q = DB::table('questions')->where('id', $id)->first();
        if (!$q) abort(404);

        $questionBankId = $q->question_bank_id;

        DB::table('question_options')->where('question_id', $id)->delete();
        DB::table('questions')->where('id', $id)->delete();

        return redirect()
            ->route('admin.question_paper_types.index', [
                'question_bank_id' => $questionBankId
            ])
            ->with('success', 'Question deleted successfully');

    } catch (\Exception $e) {
        \Log::error('Question delete error: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
// public function getQuestions(Request $request)
// {
//     $questionBankId = $request->query('question_bank_id');

//     $questionBank = QuestionBank::where('tenant_id', Auth::user()->tenant_id)
//                         ->findOrFail($questionBankId);

//     $questions = DB::table('questions')
//                     ->where('question_bank_id', $questionBankId)
//                     ->latest()
//                     ->get();

//     $questionIds = $questions->pluck('id');
//     $options     = DB::table('question_options')
//                         ->whereIn('question_id', $questionIds)
//                         ->get()
//                         ->groupBy('question_id');

//     $questions = $questions->map(function ($q) use ($options) {
//         $q->options = $options[$q->id] ?? collect();
//         return $q;
//     });

//     return response()->json([
//         'questionBank' => $questionBank,
//         'questions'    => $questions,
//     ]);
// }
public function getQuestions(Request $request)
{
    $questionBankId = $request->query('question_bank_id');

    $questionBank = QuestionBank::where('tenant_id', Auth::user()->tenant_id)
                        ->findOrFail($questionBankId);

    $questions = DB::table('questions')
                    ->where('question_bank_id', $questionBankId)
                    ->latest()
                    ->get();

    $questionIds = $questions->pluck('id');
    $options     = DB::table('question_options')
                        ->whereIn('question_id', $questionIds)
                        ->get()
                        ->groupBy('question_id');

    $questions = $questions->map(function ($q) use ($options) {
        $q->options = $options[$q->id] ?? collect();
        return $q;
    });

    return response()->json([
        'success' => true,  // Add this line for consistency
        'questionBank' => $questionBank,
        'questions'    => $questions,
    ]);
}

}