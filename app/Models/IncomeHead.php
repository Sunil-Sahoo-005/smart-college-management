<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeHead extends Model
{
    //
     protected $fillable = ['tenant_id', 'name', 'type'];

    protected $casts = [
        'type' => 'string'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function incomeRecords()
    {
        return $this->hasMany(IncomeRecord::class);
    }
}
