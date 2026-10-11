{{--
    _preview_hidden_inputs.blade.php
    Include karo dono forms mein (top + bottom confirm buttons).
    Yeh saare hidden fields pass karta hai store() tak.
--}}

{{-- Edit mode IDs --}}
@if(!empty($hiddenInputs['student_id_edit']))
    <input type="hidden" name="student_id_edit" value="{{ $hiddenInputs['student_id_edit'] }}">
@endif
@if(!empty($hiddenInputs['enrollment_id']))
    <input type="hidden" name="enrollment_id" value="{{ $hiddenInputs['enrollment_id'] }}">
@endif

{{-- Temp photo path (actual file upload store kiya tha preview() mein) --}}
@if(!empty($hiddenInputs['_temp_photo_path']))
    <input type="hidden" name="_temp_photo_path" value="{{ $hiddenInputs['_temp_photo_path'] }}">
@endif

{{-- Old photo (edit mode mein existing photo rakho agar naya upload nahi hua) --}}
@if(!empty($hiddenInputs['old_photo']))
    <input type="hidden" name="old_photo" value="{{ $hiddenInputs['old_photo'] }}">
@endif

{{-- Simple scalar fields --}}
@php
    $scalarFields = [
        'roll_number','first_name','last_name','gender','dob',
        'blood_group','category','sub_caste','religion','nationality',
        'aadhaar','email','whatsapp_number',
        'emergency_contact_name','emergency_contact_phone',
        'medical_conditions','allergies',
        'physically_handicapped','handicap_details',
        'student_status','discontinue_date','discontinue_reason','transfer_out_reason',
        'account_holder_name','bank_name','branch_name','account_type','account_number','ifsc_code',
        'academic_year_id','course_id','stream_id','class_id','section_id',
        'student_type','residence_type','enrollment_status',
        'dateof_admission','date_of_leaving',
        'hostel_id','room_id','bed_id','hostel_fee','hostel_from_date','hostel_to_date',
    ];
@endphp

@foreach($scalarFields as $field)
    @if(isset($hiddenInputs[$field]) && $hiddenInputs[$field] !== '' && $hiddenInputs[$field] !== null)
        <input type="hidden" name="{{ $field }}" value="{{ $hiddenInputs[$field] }}">
    @endif
@endforeach

{{-- fee_structure_ids — array field --}}
@if(!empty($hiddenInputs['fee_structure_ids']) && is_array($hiddenInputs['fee_structure_ids']))
    @foreach($hiddenInputs['fee_structure_ids'] as $fsId)
        <input type="hidden" name="fee_structure_ids[]" value="{{ $fsId }}">
    @endforeach
@endif