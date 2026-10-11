<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticalGroups extends Model
{
    use HasFactory;

    protected $table = 'practical_groups';

    protected $fillable = [
        'tenant_id',
        'name',
        'course_id',
        'stream_id',
        'group_master_id',
        'status',
    ];

    /* ── Relationships ─────────────────────────────────────── */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function groupMaster()
    {
        return $this->belongsTo(GroupMaster::class);
    }

    public function academicYears()
    {
        return $this->belongsToMany(
            AcademicYear::class,
            'practical_group_academic_years',
            'practical_group_id',
        );
    }

    /**
     * Students assigned to this practical group.
     * Pivot table: practical_group_students (practical_group_id, student_id)
     */
    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'practical_group_students',
            'practical_group_id',
            'student_id'
        );
    }

    /* ── URL helpers ───────────────────────────────────────── */

    public function getEditUrlAttribute()
    {
        return route('practical_groups.edit', $this);
    }

    public function getDeleteUrlAttribute()
    {
        return route('practical_groups.destroy', $this);
    }
}