<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    public function feeStructureItems()
    {
        return $this->hasMany(FeeStructureItem::class, 'fee_category_id');
    }
}
