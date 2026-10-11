<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BookIssues extends Model
{
    protected $fillable = [
        'tenant_id',
        'book_id',
        'staff_id',
        'library_card_id',
        'issue_batch_id',
        'student_id',
        'issue_date',
        'due_date',
        'return_date',
        'fine_amount',
        'status',
        'issue_duration_days', // ✅ yeh missing tha
    ];

    protected $casts = [
        'issue_date'   => 'date',
        'due_date'     => 'date',
        'return_date'  => 'date',
        'fine_amount'  => 'decimal:2', // ✅ numeric precision ke liye
        'issue_duration_days' => 'integer',
    ];

    // =====================
    // RELATIONSHIPS
    // =====================
    // ← YE ADD KARO
        public function issues()
        {
            return $this->hasMany(BookIssues::class, 'book_id');
        }
    public function book()
    {
        return $this->belongsTo(Books::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function libraryCard()
    {
        return $this->belongsTo(LibraryCards::class);
    }

    // =====================
    // SCOPES
    // =====================

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'issued')
                     ->where('due_date', '<', now());
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // =====================
    // HELPERS / ACCESSORS
    // =====================

    public function isOverdue(): bool
    {
        return $this->status === 'issued'
            && $this->due_date
            && Carbon::parse($this->due_date)->isPast();
    }

    public function getOverdueDaysAttribute(): int
    {
        if (!$this->isOverdue()) return 0;
        return (int) Carbon::parse($this->due_date)->diffInDays(now());
    }

    public function getBorrowerNameAttribute(): string
    {
        if ($this->student) {
            return $this->student->first_name . ' ' . $this->student->last_name;
        }
        if ($this->staff) {
            return $this->staff->first_name . ' ' . $this->staff->last_name;
        }
        return 'Unknown';
    }
}