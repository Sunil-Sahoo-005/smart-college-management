<table class="table table-hover mb-0">
    <thead class="table-light">
        <tr>
            <th>
                <input type="checkbox" id="select-all" onclick="toggleAll(this)">
            </th>
            <th>Roll No</th>
            <th>Student</th>
            <th>Class</th>
            <th>Section</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($enrollments as $enrollment)
            <tr>
                <td>
                    <input type="checkbox"
                           name="student_ids[]"
                           value="{{ $enrollment->student_id }}"
                           class="student-checkbox"
                           {{ in_array($enrollment->student_id, $assignedStudentIds ?? []) ? 'checked' : '' }}>
                </td>
                <td>{{ $enrollment->student->roll_number }}</td>
                <td>{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</td>
                <td>{{ $enrollment->class?->name ?? 'N/A' }}</td>
                <td>{{ $enrollment->section?->name ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No students enrolled in this branch and academic year.</td>
            </tr>
        @endforelse
    </tbody>
</table>
