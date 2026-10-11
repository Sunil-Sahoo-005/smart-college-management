<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    protected $fillable = [
        'tenant_id',
        'title',
        'author',
        'isbn',
        'copy_isbn',
        'copy_number',
        'publisher',
        'edition',
        'category',
        'year',
        'source',
        'page',
        'volume',
        'price',
        'total_copies',
        'status',
        'book_image',
        'qr_code',
        'room_id',
        'self_id',
        'row_id',
        'academic_group_id',
        'order_id',
        'bill_no',
        'bill_date',
        'withdrawal_no',
        'withdrawal_date',
        'remarks',
        'bill_pdf',
    ];

    // Relationship to AcademicGroups
    public function academicGroup()
    {
        return $this->belongsTo(AcademicGroups::class, 'academic_group_id');
    }

    // Relationship to LibraryMaster for room
    public function room()
    {
        return $this->belongsTo(LibraryMaster::class, 'room_id');
    }

    // Relationship to LibraryMaster for self
    public function self()
    {
        return $this->belongsTo(LibraryMaster::class, 'self_id');
    }

    // Relationship to LibraryMaster for row
    public function row()
    {
        return $this->belongsTo(LibraryMaster::class, 'row_id');
    }
}