<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ClassTeacher;
use App\Models\Classe;
use App\Models\Section;
use App\Models\Staff;
use App\Models\AcademicYear;
use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassTeacherController extends Controller
{
    protected $classTeacherListRoute = 'admin.class_teachers.index';

   public function index()
{
    $classTeachers = ClassTeacher::where('tenant_id', auth()->user()->tenant_id)
        ->with(['class', 'section', 'staff', 'academicYear', 'stream'])
        ->orderBy('class_id')
        ->get(); // paginate hata diya

    $streams      = Stream::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
    $classes      = Classe::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
    $sections     = Section::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
    $staff        = Staff::where('tenant_id', auth()->user()->tenant_id)->orderBy('first_name')->get();
    $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();

    $classTeachersJson = $classTeachers->map(function ($ct) {
        return [
            'id'               => $ct->id,
            'stream_name'      => $ct->stream?->name ?? 'N/A',
            'stream_id'        => $ct->stream_id,
            'class_name'       => $ct->class?->name ?? 'N/A',
            'class_id'         => $ct->class_id,
            'section_name'     => $ct->section?->name ?? 'N/A',
            'section_id'       => $ct->section_id,
            'staff_name'       => trim(($ct->staff?->first_name ?? '') . ' ' . ($ct->staff?->last_name ?? '')),
            'staff_id'         => $ct->staff_id,
            'academic_year'    => $ct->academicYear?->name ?? 'N/A',
            'academic_year_id' => $ct->academic_year_id,
            'edit_url'         => route('admin.class_teachers.edit', $ct),
            'delete_url'       => route('admin.class_teachers.destroy', $ct),
        ];
    });

    return view('Academics.class_teachers.index', compact(
        'classTeachers', 'classTeachersJson',
        'streams', 'classes', 'sections', 'staff', 'academicYears'
    ));
}

    public function create()
    {
        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();
        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $sections = Section::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $staff = Staff::where('tenant_id', auth()->user()->tenant_id)
            // ->orderBy('name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.class_teachers.form', compact('classes', 'sections', 'staff', 'academicYears', 'streams'));
    }

    public function edit(ClassTeacher $classTeacher)
    {
        $this->authorizeTenant($classTeacher);
        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();
        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $sections = Section::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $staff = Staff::where('tenant_id', auth()->user()->tenant_id)
            // ->orderBy('name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.class_teachers.form', compact('classTeacher', 'classes', 'sections', 'staff', 'academicYears','streams'));
    }

    public function store(Request $request)
    {
        $rules = [
            'class_id'         => 'required|exists:classes,id',
            'section_id'       => 'required|exists:sections,id',
            'staff_id'         => 'required|exists:staff,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ];

        $messages = [
            'class_id.required'         => 'Please select class!',
            'class_id.exists'           => 'Invalid class selected!',
            'section_id.required'       => 'Please select section!',
            'section_id.exists'         => 'Invalid section selected!',
            'staff_id.required'         => 'Please select staff!',
            'staff_id.exists'           => 'Invalid staff selected!',
            'academic_year_id.required' => 'Please select academic year!',
            'academic_year_id.exists'   => 'Invalid academic year selected!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $classTeacher = ClassTeacher::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($classTeacher)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Class teacher updated successfully',
            ];
        } else {
            $classTeacher = new ClassTeacher;
            $classTeacher->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->classTeacherListRoute),
                'msg' => 'Class teacher created successfully',
            ];
        }

        $classTeacher->stream_id         = $request->get('stream_id');
        $classTeacher->class_id         = $request->get('class_id');
        $classTeacher->section_id       = $request->get('section_id');
        $classTeacher->staff_id         = $request->get('staff_id');
        $classTeacher->academic_year_id = $request->get('academic_year_id');

        try {
            $classTeacher->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(ClassTeacher $classTeacher)
    {
        $this->authorizeTenant($classTeacher);

        try {
            $classTeacher->delete();
            return back()->with('success', 'Class teacher deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete class teacher, it may be in use.');
        }
    }

    protected function authorizeTenant(ClassTeacher $classTeacher)
    {
        if ($classTeacher->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
