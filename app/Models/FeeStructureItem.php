<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructureItem extends Model
{
    //

     protected $fillable = [
        'fee_structure_id',
        'fee_category_id',
        'amount',
        'due_month',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class);
    }
}
